
//{block name="backend/base/attribute/form"}

//{$smarty.block.parent}


//{include file="backend/avh_taxonomie/AvhGoogleTaxonomie.FieldHandler.js"}


//{include file="backend/avh_taxonomie/AvhGoogleTaxonomie.form.field.OwnType.js"}


//{include file="backend/avh_taxonomie/Shopware.form.field.AvhGoogleTaxonomieGrid.js"}


//{include file="backend/avh_taxonomie/AvhGoogleTaxonomie.MultiSelectionHandler.js"}


Ext.define('Shopware.attribute.Form-AvhGoogleTaxonomie', {
    override: 'Shopware.attribute.Form',

    registerTypeHandlers: function() {
        var handlers = this.callParent(arguments);

        handlers = Ext.Array.insert(handlers, 0, [
            Ext.create('AvhGoogleTaxonomie.MultiSelectionHandler'),
            Ext.create('AvhGoogleTaxonomie.FieldHandler')
        ]);

        return handlers;
    }
});

//{/block}