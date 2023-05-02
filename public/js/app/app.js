define([
    "dojo/Stateful",
    "dojo/_base/declare",
    "dojo/topic",
    "dojo/_base/Deferred",
    "dojo/request",
    "dojo/request/notify",
    "dojo/dom",
    "dojo/query",
    "dojo/_base/array",
    "dojo/dom-construct",
    "dojo/on",
    "dojo/_base/lang",
    "dojo/domReady!"
],
function(Stateful, declare, topic, Deferred, request, notify, dom, query, arrayUtil, domConstruct, on, lang) {
    // create App class, as subclass of Stateful
    return declare("App", [Stateful], {

        init: () => {
            topic.subscribe("system/message", function(e) {
                let markup = domConstruct.toDom('<div class="alert alert-'+ e.type +' alert-dismissible" role="alert"><div>'+ e.message +'</div><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
                domConstruct.place(markup, 'system-message');
            });
            let menuHandler = {
                id: "menuHandler",
                onClick: evt => {
                    evt.preventDefault();
                    //
                    topic.publish("system/message", {message: "Test message to test the alert system", type: "danger"});
                }
            };
            let mainNav = dom.byId("main-nav");
            on(mainNav, ".nav-link:click", lang.hitch(menuHandler, "onClick"));
        }
    });
});