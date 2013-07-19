var Revise = function(config) {
    config = config || {};
    Revise.superclass.constructor.call(this,config);
};
Ext.extend(Revise,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config:{},view:{}
});
Ext.reg('revise',Revise);

var Revise = new Revise();
