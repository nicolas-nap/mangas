import React from "react";
import { Redirect, Route } from "react-router-dom";
import { HydraAdmin, hydraDataProvider as baseHydraDataProvider, fetchHydra as baseFetchHydra, useIntrospection } from "@api-platform/admin";
import parseHydraDocumentation from "@api-platform/api-doc-parser/lib/hydra/parseHydraDocumentation";
import authProvider from "./authProvider";
import ResourceGuesser from "@api-platform/admin/lib/ResourceGuesser";
import MediaObjectsCreate from "./Resources/MediaObjects/MediaObjectsCreate";
import StaticContentsEdit from "./Resources/StaticContents/StaticContentsEdit";
import MediaObjectsList from "./Resources/MediaObjects/MediaObjectsList";
import StaticContentsCreate from "./Resources/StaticContents/StaticContentsCreate";

import MenuBookIcon from "@material-ui/icons/MenuBook";
import PermMediaOutlinedIcon from "@material-ui/icons/PermMediaOutlined";
import BathtubIcon from "@material-ui/icons/Bathtub";
import StoreOutlinedIcon from "@material-ui/icons/StoreOutlined";
import AirportShuttleIcon from '@material-ui/icons/AirportShuttle';

const entrypoint = process.env.REACT_APP_API_ENTRYPOINT;
const getHeaders = () => localStorage.getItem("token") ? {
  Authorization: `Bearer ${localStorage.getItem("token")}`,
} : {};
const fetchHydra = (url, options = {}) =>
  baseFetchHydra(url, {
    ...options,
    headers: getHeaders,
  });
const RedirectToLogin = () => {
  const introspect = useIntrospection();

  if (localStorage.getItem("token")) {
    introspect();
    return <></>;
  }
  return <Redirect to="/login" />;
};
const apiDocumentationParser = async (entrypoint) => {
  try {
    const { api } = await parseHydraDocumentation(entrypoint, { headers: getHeaders });
    return { api };
  } catch (result) {
    if (result.status === 401) {
      // Prevent infinite loop if the token is expired
      localStorage.removeItem("token");

      return {
        api: result.api,
        customRoutes: [
          <Route path="/" component={RedirectToLogin} />
        ],
      };
    }

    throw result;
  }
};


const dataProvider = baseHydraDataProvider(
  entrypoint, 
  fetchHydra, 
  apiDocumentationParser, 
  true // useEmbedded parameter
);

const fn = () => (
  <HydraAdmin
    dataProvider={ dataProvider }
    authProvider={ authProvider }
    entrypoint={ entrypoint }
  >
  </HydraAdmin>
);

export default fn;
