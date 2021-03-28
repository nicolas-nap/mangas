import { CreateGuesser, InputGuesser } from "@api-platform/admin";
import RichTextInput from 'ra-input-rich-text';

const StaticContentsCreate = props => (
  <CreateGuesser {...props}>
      <InputGuesser source="title" />
      <InputGuesser source="slug" />
      <RichTextInput source="body" validation={{ required: true }} />
  </CreateGuesser>
);

export default StaticContentsCreate;
