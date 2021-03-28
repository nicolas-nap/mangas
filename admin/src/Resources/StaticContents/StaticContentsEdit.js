import { EditGuesser, InputGuesser } from "@api-platform/admin";
import RichTextInput from 'ra-input-rich-text';

const StaticContentsEdit = props => (
  <EditGuesser {...props}>
      <InputGuesser source="title" />
      <RichTextInput source="body" validation={{ required: true }} />
  </EditGuesser>
);

export default StaticContentsEdit;
