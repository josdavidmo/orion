jQuery(function ($) {
    window.multiView = null;
    window.explorerDiv = $('.data-explorer-here');

    // create the demo dataset
    var dataset = createDemoDataset();
    // now create the multiview
    // this is rather more elaborate than the minimum as we configure the
    // MultiView in various ways (see function below)
    window.multiview = createMultiView(dataset);
});

// create standard demo dataset
function createDemoDataset() {
    var dataset = new recline.Model.Dataset({
        records: data,
        // let's be really explicit about fields
        // Plus take opportunity to set date to be a date field and set some labels
        fields: fields,
    });
    return dataset;
}

// make MultivView
//
// creation / initialization in a function so we can call it again and again
var createMultiView = function (dataset, state) {
    // remove existing multiview if present
    var reload = false;
    if (window.multiView) {
        window.multiView.remove();
        window.multiView = null;
        reload = true;
    }

    var $el = $('<div />');
    $el.appendTo(window.explorerDiv);

    // customize the subviews for the MultiView
    var views = [
        {
            id: 'grid',
            label: 'Grid',
            view: new recline.View.SlickGrid({
                model: dataset,
                state: {
                    gridOptions: {
                        editable: false,
                        // Enable support for row add
                        enabledAddRow: false,
                        // Enable support for row delete
                        enabledDelRow: false,
                        // Enable support for row ReOrder 
                        enableReOrderRow: true,
                        autoEdit: false,
                        enableCellNavigation: true
                    },
                    columnsEditor: [
                        {column: 'date', editor: Slick.Editors.Date},
                        {column: 'sometext', editor: Slick.Editors.Text}
                    ]
                }
            })
        },
        {
            id: 'graph',
            label: 'Gráfica',
            view: new recline.View.Graph({
                model: dataset

            })
        },
        {
            id: 'map',
            label: 'Mapa',
            view: new recline.View.Map({
                model: dataset
            })
        }
    ];

    var multiView = new recline.View.MultiView({
        model: dataset,
        el: $el,
        state: state,
        views: views
    });
    return multiView;
}