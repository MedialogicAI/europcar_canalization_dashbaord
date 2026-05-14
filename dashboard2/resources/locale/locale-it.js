/**
 * This is the (missing) reference point for translations
 * I searched for all strings surrounded by <locale> tag in src folders
 * Also added src/data/validator/* strings (missing <locale> tag)
 *
 * File order is by file path
 *
 * Charset is 0x1f-0x7f for safe encoding, verify with regex [\x7f-\xff]
 * Local reference:
 * \u00E0 = a'    &agrave;
 * \u00E8 = e'    &egrave;
 * \u00EC = i'    &igrave;
 * \u00F2 = o'    &ograve;
 * \u00F9 = u'    &ugrave;
 * \u20ac = Euro  &euro;
 *
 * Date Reference: http://docs.sencha.com/extjs/6.0.2-classic/Ext.Date.html
 *
 * Italian translation
 * 2016-06-28 updated by Fabio De Paolis (update to ExtJs 6.0.2)
 * 2012-05-28 updated by Fabio De Paolis (many changes, update to 4.1.0)
 * 2007-12-21 updated by Federico Grilli
 * 2007-10-04 updated by eric_void
 */
Ext.onReady(function () {

  if (Ext.Date) {
    Ext.Date.monthNames = ["Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre"];

    Ext.Date.getShortMonthName = function (month) {
      return Ext.Date.monthNames[month].substring(0, 3);
    };

    Ext.Date.monthNumbers = {
      Gen: 0,
      Feb: 1,
      Mar: 2,
      Apr: 3,
      Mag: 4,
      Giu: 5,
      Lug: 6,
      Ago: 7,
      Set: 8,
      Ott: 9,
      Nov: 10,
      Dic: 11
      // These are not required, Docs should be updated
      // Gennaio: 0,
      // Febbraio: 1,
      // Marzo: 2,
      // Aprile: 3,
      // Maggio: 4,
      // Giugno: 5,
      // Luglio: 6,
      // Agosto: 7,
      // Settembre: 8,
      // Ottobre: 9,
      // Novembre: 10,
      // Dicembre: 11
    };

    Ext.Date.getMonthNumber = function (name) {
      return Ext.Date.monthNumbers[name.substring(0, 1).toUpperCase() + name.substring(1, 3).toLowerCase()];
    };

    Ext.Date.dayNames = ["Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato"];

    Ext.Date.getShortDayName = function (day) {
      return Ext.Date.dayNames[day].substring(0, 3);
    };
  }

  if (Ext.util && Ext.util.Format) {
    Ext.apply(Ext.util.Format, {
      thousandSeparator: '.', // ',',
      decimalSeparator: ',', // '.',
      // currencyPrecision: 2,
      currencySign: '\u20ac', // '$', // Euro
      // currencyAtEnd: false,
      defaultDateFormat: 'd/m/Y' // 'm/d/Y',
    });
  }
});

// This is needed until we can refactor all of the locales into individual files
Ext.define("Ext.locale.it.Component", {
  override: "Ext.Component"
});

// src/LoadMask.js
Ext.define("Ext.locale.it.LoadMask", {
  override: "Ext.LoadMask",
  msg: "Caricamento..." // 'Loading...',
});

// src/data/validator/Bound.js
Ext.define("Ext.locale.it.data.validator.Bound", {
  override: "Ext.data.validator.Bound",
  emptyMessage: "Obbligatorio" // "Must be present"
});

// src/data/validator/Email.js
Ext.define("Ext.locale.it.data.validator.Email", {
  override: "Ext.data.validator.Email",
  message: "Non \u00E8 un indirizzo email valido" // "Is not a valid email address"
});

// src/data/validator/Exclusion.js
Ext.define("Ext.locale.it.data.validator.Exclusion", {
  override: "Ext.data.validator.Exclusion",
  message: "È un valore che \u00E8 stato escluso" // "Is a value that has been excluded"
});

// src/data/validator/Format.js
Ext.define("Ext.locale.it.data.validator.Format", {
  override: "Ext.data.validator.Format",
  message: "È nel formato errato" // "Is in the wrong format"
});

// src/data/validator/Inclusion.js
Ext.define("Ext.locale.it.data.validator.Inclusion", {
  override: "Ext.data.validator.Inclusion",
  message: "Non \u00E8 nell'elenco dei valori consentiti" // "Is not in the list of acceptable values"
});

// src/data/validator/Length.js
Ext.define("Ext.locale.it.data.validator.Length", {
  override: "Ext.data.validator.Length",
  minOnlyMessage: "Lunghezza minima {0}", // "Length must be at least {0}",
  maxOnlyMessage: "Lunghezza massima {0}", // "Length must be no more than {0}",
  bothMessage: "Lunghezza compresa tra {0} e {1}" // "Length must be between {0} and {1}"
});

// src/data/validator/Presence.js
Ext.define("Ext.locale.it.data.validator.Presence", {
  override: "Ext.data.validator.Presence",
  message: "Obbligatorio" // "Must be present"
});

// src/data/validator/Range.js
Ext.define("Ext.locale.it.data.validator.Range", {
  override: "Ext.data.validator.Range",
  minOnlyMessage: "Deve essere minimo {0}", // "Must be must be at least {0}",
  maxOnlyMessage: "Deve essere massimo {0}", // "Must be no more than than {0}",
  bothMessage: "Deve essere compreso tra {0} e {1}", // "Must be between {0} and {1}",
  nanMessage: "Deve essere un valore numerico" // "Must be numeric"
});

// src/form/Basic.js
Ext.define("Ext.locale.it.form.Basic", {
  override: "Ext.form.Basic",
  waitTitle: "Attendere..." // 'Please Wait...',
});

// src/form/CheckboxGroup.js
Ext.define("Ext.locale.it.form.CheckboxGroup", {
  override: "Ext.form.CheckboxGroup",
  blankText: "Selezionare almeno un elemento nel gruppo" // "You must select at least one item in this group",
});

// src/form/FieldContainer.js
//Ext.define("Ext.locale.it.form.FieldContainer", {
//  override: "Ext.form.FieldContainer",
//  labelConnector: ', '
//});

// src/form/FieldSet.js
Ext.define("Ext.locale.it.form.FieldSet", {
  override: "Ext.form.FieldSet",
  descriptionText: "{0} Gruppo", // '{0} field set',
  expandText: "Espandi il Gruppo" // 'Expand field set',
});

// src/form/Lebelable.js
//Ext.define("Ext.locale.it.form.Lebelable", {
//  override: "Ext.form.Lebelable",
//  labelSeparator : ':'
//});

// src/form/RadioGroup.js
Ext.define("Ext.locale.it.form.RadioGroup", {
  override: "Ext.form.RadioGroup",
  blankText: "Selezionare un elemento nel gruppo" // 'You must select one item in this group',
});

// src/form/field/Base.js
Ext.define("Ext.locale.it.form.field.Base", {
  override: "Ext.form.field.Base",
  invalidText: "Valore non valido" // 'The value in this field is invalid',
});

// src/form/field/Base.js
Ext.define("Ext.locale.it.form.field.Base", {
  override: "Ext.form.field.Base",
  invalidText: "Valore non valido" // 'The value in this field is invalid',
  //formatText
});

// src/form/field/Base.js
Ext.define("Ext.locale.it.form.field.Base", {
  override: "Ext.form.field.Base",
  invalidText: "Valore non valido" // 'The value in this field is invalid',
  //formatText
});

// src/form/field/ComboBox.js
//Ext.define("Ext.locale.it.form.field.ComboBox", {
//  override: "Ext.form.field.ComboBox",
//  delimiter: ', ',
//});

// src/form/field/Date.js
Ext.define("Ext.locale.it.form.field.Date", {
  override: "Ext.form.field.Date",
  format: "d/m/Y", // "m/d/Y",
  ariaFormat: 'M j Y', // 'M j Y',
  altFormats: "d-m-y|d-m-Y|d/m|d-m|dm|dmy|dmY|d|Y-m-d", // "m/d/Y|n/j/Y|n/j/y|m/j/y|n/d/y|m/j/Y|n/d/Y|m-d-y|m-d-Y|m/d|m-d|md|mdy|mdY|d|Y-m-d|n-j|n/j",
  disabledDaysText: "Disabilitato", // "Disabled",
  ariaDisabledDaysText: "Questo giorno \u00E8 disabilitato", // "This day of week is disabled",
  disabledDatesText: "Disabilitato", // "Disabled",
  ariaDisabledDatesText: "Questa data non pu\u00F2 essere selezionata", // "This date cannot be selected",
  minText: "La data deve essere maggiore o uguale a {0}", // "The date in this field must be equal to or after {0}",
  ariaMinText: "La data deve essere maggiore o uguale a {0}", // "The date must be equal to or after {0}",
  maxText: "La data deve essere minore o uguale a {0}", // "The date in this field must be equal to or before {0}",
  ariaMaxText: "La data deve essere minore o uguale a {0}", // "The date must be equal to or before {0}",
  invalidText: "{0} non \u00E8 una data valida, deve essere nel formato {1}", // "{0} is not a valid date - it must be in the format {1}",
  formatText: "Il formato richiesto \u00E8 {1}", // 'Expected date format: {0}',
  startDay: 1 // `0` (Sunday).
});

// src/form/field/File.js
Ext.define("Ext.locale.it.form.field.File", {
  override: "Ext.form.field.File",
  buttonText: 'Scegli...' // 'Browse...',
});

// src/form/field/HtmlEditor.js
Ext.define("Ext.locale.it.form.field.HtmlEditor", {
  override: "Ext.form.field.HtmlEditor",
  createLinkText: 'Inserire un URL per il link:', // 'Please enter the URL for the link:',
  // buttonTips as Object works, tested
  buttonTips: {
    bold: {
      title: 'Grassetto (Ctrl+B)', // 'Bold (Ctrl+B)',
      text: 'Testo selezionato in Grassetto.', // 'Make the selected text bold.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    },
    italic: {
      title: 'Corsivo (Ctrl+I)', // 'Italic (Ctrl+I)',
      text: 'Testo selezionato in Corsivo.', // 'Make the selected text italic.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    },
    underline: {
      title: 'Sottolinea (Ctrl+U)', // 'Underline (Ctrl+U)',
      text: 'Sottolinea il testo selezionato.', // 'Underline the selected text.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    },
    increasefontsize: {
      title: 'Ingrandisci testo', // 'Grow Text',
      text: 'Aumenta la dimensione del carattere.', // 'Increase the font size.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    },
    decreasefontsize: {
      title: 'Riduci testo', // 'Shrink Text',
      text: 'Diminuisce la dimensione del carattere.', // 'Decrease the font size.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    },
    backcolor: {
      title: 'Colore evidenziazione testo', // 'Text Highlight Color',
      text: 'Modifica il colore di sfondo del testo selezionato.', // 'Change the background color of the selected text.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    },
    forecolor: {
      title: 'Colore carattere', // 'Font Color',
      text: 'Modifica il colore del testo selezionato.', // 'Change the color of the selected text.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    },
    justifyleft: {
      title: 'Allinea a sinistra', // 'Align Text Left',
      text: 'Allinea il testo a sinistra.', // 'Align text to the left.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    },
    justifycenter: {
      title: 'Centra', // 'Center Text',
      text: 'Centra il testo.', // 'Center text in the editor.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    },
    justifyright: {
      title: 'Allinea a destra', // 'Align Text Right',
      text: 'Allinea il testo a destra.', // 'Align text to the right.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    },
    insertunorderedlist: {
      title: 'Elenco puntato', // 'Bullet List',
      text: 'Inserisci un elenco puntato.', // 'Start a bulleted list.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    },
    insertorderedlist: {
      title: 'Elenco numerato', // 'Numbered List',
      text: 'Inserisci un elenco numerato.', // 'Start a numbered list.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    },
    createlink: {
      title: 'Collegamento', // 'Hyperlink',
      text: 'Trasforma il testo selezionato in un collegamanto.', // 'Make the selected text a hyperlink.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    },
    sourceedit: {
      title: 'Sorgente', // 'Source Edit',
      text: 'Passa alla modalit\u00E0 modifica del sorgente.', // 'Switch to source editing mode.',
      cls: Ext.baseCSSPrefix + 'html-editor-tip'
    }
  }
});

// src/form/field/Number.js
Ext.define("Ext.locale.it.form.field.Number", {
  override: "Ext.form.field.Number",
  // decimalSeparator : null,
  // submitLocaleSeparator: true,
  // decimalPrecision : 2,
  minText: "Il valore minimo \u00E8 {0}", // 'The minimum value for this field is {0}',
  maxText: "Il valore massimo \u00E8 {0}", // 'The maximum value for this field is {0}',
  nanText: "{0} non \u00E8 un valore numerico valido", // '{0} is not a valid number',
  negativeText: "Il valore non pu\u00F2 essere negativo" // 'The value cannot be negative',
});

// src/form/field/Text.js
Ext.define("Ext.locale.it.form.field.Text", {
  override: "Ext.form.field.Text",
  // growAppend: 'W',
  minLengthText: "La lunghezza minima \u00E8 {0}", // 'The minimum length for this field is {0}',
  maxLengthText: "La lunghezza massima \u00E8 {0}", // 'The maximum length for this field is {0}',
  blankText: "Campo obbligatorio" // 'This field is required',
});

// src/form/field/Time.js
Ext.define("Ext.locale.it.form.field.Time", {
  override: "Ext.form.field.Time",
  minText: "L'ora deve essere maggiore o uguale a {0}", // "The time in this field must be equal to or after {0}",
  maxText: "L'ora deve essere minore o uguale a {0}", // "The time in this field must be equal to or before {0}",
  invalidText: "{0} non \u00E8 un Orario valido", // "{0} is not a valid time",
  format: "H:i", // "g:i A",
  // altFormats: "g:ia|g:iA|g:i a|g:i A|h:i|g:i|H:i|ga|ha|gA|h a|g a|g A|gi|hi|gia|hia|g|H|gi a|hi a|giA|hiA|gi A|hi A",
  formatText: "Il formato richiesto \u00E8 HH:MM" // 'Expected time format: HH:MM space am/pm',
});

// src/form/field/VTypes.js
Ext.define("Ext.locale.it.form.field.VTypes", {
  override: "Ext.form.field.VTypes",
  emailText: 'Il campo deve essere un indirizzo e-mail nel formato "nome@esempio.it"', // 'This field should be an e-mail address in the format "user@example.com"',
  urlText: 'Il campo deve essere un indirizzo URL nel formato "http:/' + '/www.esempio.it"', // 'This field should be a URL in the format "http:/'+'/www.example.com"',
  alphaText: 'Il campo deve contenere solo lettere e _', // 'This field should only contain letters and _',
  alphanumText: 'Il campo deve contenere solo lettere, numeri e _' // 'This field should only contain letters, numbers and _',
});

// src/grid/RowEditor.js
Ext.define("Ext.locale.it.grid.RowEditor", {
  override: "Ext.grid.RowEditor",
  saveBtnText: 'Invia', // 'Update',
  cancelBtnText: 'Annulla', // 'Cancel',
  errorsText: 'Errori', // 'Errors',
  dirtyText: 'Confermare o annullare i cambiamenti' // 'You need to commit or cancel your changes',
});

// src/grid/column/Boolean.js
Ext.define("Ext.locale.it.grid.column.Boolean", {
  override: "Ext.grid.column.Boolean",
  trueText: "vero", // 'true',
  falseText: "falso" // 'false',
});

// src/grid/column/Date.js
// Ext.define("Ext.locale.it.grid.column.Date", {
//   override: "Ext.grid.column.Date",
//   format: 'd/m/Y'
// });

// src/grid/column/Number.js
Ext.define("Ext.locale.it.grid.column.Number", {
  override: "Ext.grid.column.Number",
  format: '0.000,00' // '0,000.00',
});

// src/grid/feature/Grouping.js
Ext.define("Ext.locale.it.grid.feature.Grouping", {
  override: "Ext.grid.feature.Grouping",
  groupByText: 'Raggruppa per questo campo', // 'Group by this field',
  showGroupsText: 'Mostra nei gruppi', // 'Show in groups',
  expandTip: 'Clicca per espandere. Con il tasto CTRL riduce tutti gli altri', // 'Click to expand. CTRL key collapses all others',
  collapseTip: 'Clicca per ridurre. Con il tasto CTRL espande tutti gli altri' // 'Click to collapse. CTRL/click collapses all others',
});

// src/grid/filters/Filters.js
Ext.define("Ext.locale.it.grid.filters.Filters", {
  override: "Ext.grid.filters.Filters",
  menuFilterText: 'Filtri' // 'Filters',
});

// src/grid/filters/filter/Boolean.js
Ext.define("Ext.locale.it.grid.filters.filter.Boolean", {
  override: "Ext.grid.filters.filter.Boolean",
  yesText: 'Si', // 'Yes',
  noText: 'No' // 'No',
});

// src/grid/filters/filter/Date.js
Ext.define("Ext.locale.it.grid.filters.filter.Date", {
  override: "Ext.grid.filters.filter.Date",
  // fields as Object will not work (because inside config block), use getFields function
  // fields: {
  //   lt: {text: 'Prima del'}, // {text: 'Before'},
  //   gt: {text: 'Dopo il'}, // {text: 'After'},
  //   eq: {text: 'Il giorno'} // {text: 'On'}
  // },
  getFields: function () {
    return {
      lt: {text: 'Prima del'}, // {text: 'Before'},
      gt: {text: 'Dopo il'}, // {text: 'After'},
      eq: {text: 'Il giorno'} // {text: 'On'}
    };
  }
});

// src/grid/filters/filter/List.js
Ext.define("Ext.locale.it.grid.filters.filter.List", {
  override: "Ext.grid.filters.filter.List",
  loadingText: 'Caricamento...' // 'Loading...',
});

// src/grid/filters/filter/Number.js
Ext.define("Ext.locale.it.grid.filters.filter.Number", {
  override: "Ext.grid.filters.filter.Number",
  emptyText: 'Inserisci il Numero...' // 'Enter Number...',
});

// src/grid/filters/filter/String.js
Ext.define("Ext.locale.it.grid.filters.filter.String", {
  override: "Ext.grid.filters.filter.String",
  emptyText: 'Inserisci il Valore...' // 'Enter Filter Text...',
});

// src/grid/header/Container.js
Ext.define("Ext.locale.it.grid.header.Container", {
  override: "Ext.grid.header.Container",
  sortAscText: "Ordinamento Crescente", // 'Sort Ascending',
  sortDescText: "Ordinamento Decrescente", // 'Sort Descending',
  sortClearText: "Senza Ordinamento naturale",
  columnsText: "Colonne" // 'Columns',
});

// src/grid/locking/Lockable.js
Ext.define("Ext.locale.it.grid.locking.Lockable", {
  override: "Ext.grid.Lockable",
  lockText: "Blocca colonna", // 'Lock',
  unlockText: "Sblocca colonna" // 'Unlock',
});

// src/grid/plugin/DragDrop.js
Ext.define("Ext.locale.it.grid.plugin.DragDrop", {
  override: "Ext.grid.plugin.DragDrop",
  dragText: "{0} Righe selezionate" // '{0} selected row{1}',
});

// src/grid/property/HeaderContainer.js
Ext.define("Ext.locale.it.grid.property.HeaderContainer", {
  override: "Ext.grid.property.HeaderContainer",
  nameText: 'Nome', // 'Name',
  valueText: 'Valore', // 'Value',
  dateFormat: 'Formato Data', // 'm/j/Y',
  trueText: 'Vero', // 'true',
  falseText: 'Falso' // 'false'
});

// src/menu/CheckItem.js
Ext.define("Ext.locale.it.menu.CheckItem", {
  override: 'Ext.menu.CheckItem',
  submenuText: '{0} sottomenu' // '{0} submenu',
});

// src/menu/DatePicker.js
Ext.define("Ext.locale.it.menu.DatePicker", {
  override: 'Ext.menu.DatePicker',
  ariaLabel: 'Scegli Data' // 'Date picker',
});

// src/panel/Panel.js
Ext.define("Ext.locale.it.panel.Panel", {
  override: 'Ext.panel.Panel',
  closeToolText: 'Chiudi', // 'Close panel',
  collapseToolText: 'Riduci', // 'Collapse panel',
  expandToolText: 'Espandi' // 'Expand panel',
});

// src/picker/Date.js
Ext.define("Ext.locale.it.picker.Date", {
  override: 'Ext.picker.Date',
  todayText: 'Oggi', // 'Today',
  ariaTitle: 'Scegli Data: {0}', // 'Date Picker: {0}',
  ariaTitleDateFormat: 'F d', // 'F d',
  todayTip: '{0} (Barra spaziatrice)', // '{0} (Spacebar)',
  minText: 'Data precedente alla data minima', // 'This date is before the minimum date',
  ariaMinText: 'La data \u00E8 minore di quella minima consentita', // "This date is before the minimum date",
  maxText: 'Data successiva alla data massima', // 'This date is after the maximum date',
  ariaMaxText: 'La data \u00E8 maggiore di quella massima consentita', // "This date is after the maximum date",
  disabledDaysText: 'Disabilitato', // 'Disabled',
  ariaDisabledDaysText: 'Questo giorno \u00E8 disabilitato', // "This day of week is disabled",
  disabledDatesText: 'Disabilitato', // 'Disabled',
  ariaDisabledDatesText: 'Questa data \u00E8 disabilitata', // "This date is disabled",
  nextText: 'Mese successivo (CTRL+Destra)', // 'Next Month (Control+Right)',
  prevText: 'Mese precedente (CTRL+Sinistra)', // 'Previous Month (Control+Left)',
  monthYearText: 'Scegli un Mese (CTRL+Sopra/Sotto per cambiare anno)', // 'Choose a month (Control+Up/Down to move years)',
  monthYearFormat: 'F Y', // 'F Y',
  startDay: 1, // 0,
  // showToday: true,
  longDayFormat: 'd F Y' // 'F d, Y',
  // getDayInitial: function(value) {
  //   return value.substr(0,1);
  // },
});

// src/picker/Month.js
Ext.define("Ext.locale.it.picker.Month", {
  override: "Ext.picker.Month",
  okText: 'OK', // 'OK',
  cancelText: 'Annulla' // 'Cancel',
});

// src/picker/Time.js
Ext.define("Ext.locale.it.picker.Time", {
  override: "Ext.picker.Time",
  format: "H:i" // "g:i A",
});

// src/tab/Tab.js
Ext.define("Ext.locale.it.tab.Tab", {
  override: "Ext.tab.Tab",
  closeText: 'Rimuovibile' // 'removable', The wording is chosen to be less confusing to blind users.
});

// src/toolbar/Paging.js
Ext.define("Ext.locale.it.toolbar.Paging", {
  override: 'Ext.toolbar.Paging',
  displayMsg: 'Visualizzati {0} - {1} di {2}', // 'Displaying {0} - {1} of {2}',
  emptyMsg: 'Nessun record', // 'No data to display',
  beforePageText: 'Pagina', // 'Page',
  afterPageText: 'di {0}', // 'of {0}',
  firstText: 'Prima pagina', // 'First Page',
  prevText: 'Pagina precedente', // 'Previous Page',
  nextText: 'Pagina successiva', // 'Next Page',
  lastText: 'Ultima pagina', // 'Last Page',
  refreshText: 'Aggiorna' // 'Refresh',
});

// src/tree/plugin/TreeViewDragDrop.js
Ext.define("Ext.locale.it.tree.plugin.TreeViewDragDrop", {
  override: 'Ext.tree.plugin.TreeViewDragDrop',
  dragText: '{0} nodi selezionati' // '{0} selected node{1}',
});

// src/view/AbstractView.js
Ext.define("Ext.locale.it.view.AbstractView", {
  override: "Ext.view.AbstractView",
  loadingText: "Caricamento..." // 'Loading...',
  // emptyText: "",
});

// src/window/MessageBox.js
Ext.define("Ext.locale.it.window.MessageBox", {
  override: "Ext.window.MessageBox",
  buttonText: {
    ok: "OK", // 'OK',
    cancel: "Annulla", // 'Cancel'
    yes: "Si", // 'Yes',
    no: "No" // 'No',
  },
  titleText: {
    confirm: 'Conferma', // 'Confirm',
    prompt: 'Richiesta', // 'Prompt',
    wait: 'Attendere...', // 'Loading...',
    alert: 'Attenzione' // 'Attention'
  }
});
