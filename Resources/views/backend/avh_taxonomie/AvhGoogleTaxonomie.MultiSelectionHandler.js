//
Ext.define('AvhGoogleTaxonomie.MultiSelectionHandler', {
    extend: 'Shopware.attribute.AbstractEntityFieldHandler',
    entity: "AvhGoogleTaxonomie\\Models\\AvhGoogleTaxonomie",
    singleSelectionClass: 'Shopware.form.field.SingleSelection',
    multiSelectionClass: 'Shopware.form.field.AvhGoogleTaxonomieGrid'
});