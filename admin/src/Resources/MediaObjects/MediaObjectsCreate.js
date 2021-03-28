import {
    CreateGuesser,
} from "@api-platform/admin";
import { FileField, FileInput } from "react-admin";

const MediaObjectsCreate = props => (
    <CreateGuesser {...props}>
        <FileInput source="file">
            <FileField source="src" title="title" />
        </FileInput>
    </CreateGuesser>
);

export default MediaObjectsCreate;