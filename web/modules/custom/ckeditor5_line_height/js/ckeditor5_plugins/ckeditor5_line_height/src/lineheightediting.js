import { Plugin } from 'ckeditor5/src/core';
import { buildDefinition, isSupported } from './utils';
import LineHeightCommand from './lineheightcommand';

export default class LineHeightEditing extends Plugin {
  constructor(editor) {
    super(editor);

    editor.config.define('lineHeight', {
      options: [0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5, 5.5, 6, 6.5],
    });
  }

  /**
   * @inheritDoc
   */
  init() {
    const editor = this.editor;
    const schema = editor.model.schema;
    const enabledOptions = editor.config.get('lineHeight.options').map(option => String(option)).filter(isSupported); // filter
    schema.extend('$block', { allowAttributes: 'lineHeight' });
    editor.model.schema.setAttributeProperties('lineHeight', { isFormatting: true });
    const definition = buildDefinition(enabledOptions/* .filter( option => !isDefault( option ) ) */);
    editor.conversion.attributeToAttribute(definition);
    editor.commands.add('lineHeight', new LineHeightCommand(editor));
  }
}
