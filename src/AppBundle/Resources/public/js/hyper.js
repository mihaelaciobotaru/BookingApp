/* (C) TANASE COSMIN ROMEO http://tcrhd.net */

var hyper = {
    params: {
        debug: true
    },
    components: {
        "swig": {
            loaded: false,
            stylesheets: { },
            javascripts: {
                "https://cdnjs.cloudflare.com/ajax/libs/swig/1.4.1/swig.min.js": false
            }
        },
        "bootstrap": {
            loaded: true,
            stylesheets: {
                "/bundles/app/theme/bootstrap/css/bootstrap.css": false,
            },
            javascripts: {
                "/bundles/app/theme/bootstrap/js/bootstrap.js": false
            }
        },
        "bootbox": {
            loaded: false,
            dependencies: ["bootstrap"],
            javascripts: {
                "https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js": false
            }
        },
        "x-editable": {
            loaded: false,
            dependencies: ["bootstrap"],
            stylesheets: {
                "https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css": false,
            },
            javascripts: {
                "https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js": false
            }
        },
        "toastr": {
            loaded: false,
            stylesheets: {
                "https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.2/toastr.min.css": false,
            },
            javascripts: {
                "https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.2/toastr.min.js": false,
            },
            config: function () {
                toastr.options={closeButton:!1,debug:!1,newestOnTop:!0,progressBar:!0,positionClass:"toast-top-right",preventDuplicates:!1,onclick:null,showDuration:"300",hideDuration:"1000",timeOut:"20000",extendedTimeOut:"1000",showEasing:"swing",hideEasing:"linear",showMethod:"fadeIn",hideMethod:"fadeOut"};
            }
        },
    },
    load: function (component, callback){
        if (hyper.components[component]){ //We only attempt loading the component if it's defined
            if (hyper.components[component].loaded!=true){ //We do not double check the loading of components
                //Load Dependencies
                for (i in hyper.components[component].dependencies){
                    var dependency = hyper.components[component].dependencies[i];
                    if (hyper.components[dependency]){ //We only attempt loading the dependency if it's defined
                        if (hyper.components[dependency].loaded==false){
                            hyper.load(dependency, function (){ hyper.load(component, callback); });
                            return false;
                        }
                    } else {
                        console.log("Dependency "+dependency+" for "+component+" not found!");
                    }
                }

                //Load Stylesheets Async
                for (style in hyper.components[component].stylesheets){
                    if (hyper.components[component].stylesheets[style]==false){
                        hyper.components[component].stylesheets[style] = true;
                        $('<link/>', {rel: 'stylesheet', href: style}).appendTo('head');
                        if (hyper.params.debug){ console.log("Load Stylesheets "+component+"/"+style); }
                    }
                }

                //Load Javascripts Required by the Component
                for (script in hyper.components[component].javascripts){
                    if (hyper.components[component].javascripts[script]==false){
                        if (hyper.params.debug){ console.log("Load Script "+component+"/"+script); }
                        $.ajax({
                            url: script,
                            cache: true,
                            context: {component: component, script: script},
                            dataType: "script",
                            success: function (){
                                if (hyper.params.debug){ console.log("\t    "+this.component+"/"+this.script+" loaded"); }
                                hyper.components[this.component].javascripts[this.script] = true;
                                hyper.load(this.component, callback);
                            }
                        });
                        return false;//Component is not loaded yet
                    }
                }

                hyper.components[component].loaded = true;
            }

            if (hyper.components[component].config){
                hyper.components[component].config();
            }

            callback();
        } else { //This component is not defined
            console.log("ERROR. Component "+component+" not defined");
        }
    }
};

//////////////////////////////////////////////// Preload functions
//Preload Bootbox
var bootbox = {
    alert: function (message, callback){ hyper.load("bootbox", function (){ bootbox.alert(message, callback); }) },
    prompt: function (message, callback){ hyper.load("bootbox", function (){ bootbox.prompt(message, callback); }) },
    confirm: function (message, callback){ hyper.load("bootbox", function (){ bootbox.confirm(message, callback); }) },
    dialog: function (options){ hyper.load("bootbox", function (){ bootbox.dialog(options); }) },
}

//Preload SWIG
var swig = {
    run: function (file, options, path){ hyper.load("swig", function (){ swig.run(file, options, path); }); },
    render: function (tpl, options){ hyper.load("swig", function (){ swig.render(tpl, options); }); },
}

//Preload Bootstrap X-Editable
jQuery.fn.extend({ editable: function (options){ hyper.load("x-editable", function (){ $(this).editable(options); }.bind(this)); }});

//Preload Ajax File Uploader
jQuery.fn.extend({ fileUpload: function (options){ hyper.load("ajax-fileupload", function (){ $(this).fileUpload(options); }.bind(this)); }});

//Preload Rateit
jQuery.fn.extend({ rateit: function (options){ hyper.load("rateit", function (){ $(this).rateit(options); }.bind(this)); }});

//Preload Toastr
var toastr = {
    success: function (message, title, options){ hyper.load("toastr", function (){ toastr["success"](message, title, options); }) },
    info: function (message, title, options){ hyper.load("toastr", function (){ toastr["info"](message, title, options); }) },
    warning: function (message, title, options){ hyper.load("toastr", function (){ toastr["warning"](message, title, options); }) },
    error: function (message, title, options){ hyper.load("toastr", function (){ toastr["error"](message, title, options); }) },
}

//Preload Autocomplete
jQuery.fn.extend({ autocomplete: function (options){ hyper.load("autocomplete", function (){ $(this).autocomplete(options);}.bind(this)); }});
