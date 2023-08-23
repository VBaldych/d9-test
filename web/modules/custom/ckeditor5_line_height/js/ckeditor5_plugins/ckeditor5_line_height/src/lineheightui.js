import { Plugin } from 'ckeditor5/src/core';
import { addListToDropdown, createDropdown, Model } from 'ckeditor5/src/ui';
import { Collection } from 'ckeditor5/src/utils';
import { isSupported, normalizeOptions } from './utils';
import Icon from '../theme/icons/lineHeight.svg';

export default class LineHightUI extends Plugin {
  init() {
    const editor = this.editor;
    const t = editor.t;
    const options = this._getLocalizedOptions();
    const command = editor.commands.get('lineHeight');

    editor.ui.componentFactory.add('lineHeight', locale => {
      const dropdownView = createDropdown(locale);
      addListToDropdown(dropdownView, _prepareListOptions(options, command));

      dropdownView.buttonView.set({
        label: t('Line Height'),
        icon: Icon,
        tooltip: true,
      });

      dropdownView.extendTemplate({
        attributes: {
          class: [
            'ckeditor5-lineHeight-dropdown',
          ],
        },
      });

      dropdownView.bind('isEnabled').to(command);

      this.listenTo(dropdownView, 'execute', evt => {
        editor.execute(evt.source.commandName, { value: evt.source.commandParam });
        editor.editing.view.focus();
      });

      return dropdownView;
    });
  }

  _getLocalizedOptions() {
    const editor = this.editor;
    const t = editor.t;

    const localizedTitles = {
      Default: t('Default'),
    };

    const options = normalizeOptions(editor.config.get('lineHeight.options').filter(option => isSupported(option)));

    return options.map(option => {
      const title = localizedTitles[option.title];

      if (title && title != option.title) {
        option = Object.assign({}, option, { title });
      }

      return option;
    });
  }
}

function _prepareListOptions(options, command) {
  const itemDefinitions = new Collection();

  for (const option of options) {
    const def = {
      type: 'button',
      model: new Model({
        commandName: 'lineHeight',
        commandParam: option.model,
        label: option.title,
        class: 'ckeditor5-lineHeight-dropdown',
        withText: true,
      }),
    };

    if (option.view && option.view.classes) {
      def.model.set('class', `${def.model.class} ${option.view.classes}`);
    }

    def.model.bind('isOn').to(command, 'value', value => {
      const newValue = value ? parseFloat(value) : value;
      return newValue === option.model;
    });

    itemDefinitions.add(def);
  }

  return itemDefinitions;
}
