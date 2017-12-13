(function(){"use strict";jQuery(document).ready(function(a){var b,c,d,e,f,g,h,i;return i=null!=(g=window.typenow)?g:"",f=null!=(h=window.pagenow)?h:"","shop_order"===i&&(a(".wf_pklist_print_document").each(function(){var b,c,d,e;return d=a(this).closest("tr"),c=a(this).closest("td p"),b=d.find(".wf-pklist-print-tooltip-order-actions"),e=a(this),a(this).remove(),c.append(e),c.find("a.wf_pklist_print_document").tipTip({activation:"click",content:b.html(),keepAlive:!0,defaultPosition:"left",edgeOffset:2,fadeIn:10})}),null!=a("select[name=wc_order_action]")&&(e=a("select[name=wc_order_action]"),c=e.find("option[value^=wf_pklist_]").detach(),c.length&&(b=a("<optgroup>").attr("label").append(c),e.append(b))),a(".wc-reload").on("click",function(b){var c,d;return c}))})}).call(this);
(function() {
    "use strict";
    jQuery(document).ready(function(a) {
        var b, c, d, e, f, g, h, i;
        return i = null != (g = window.typenow) ? g : "", f = null != (h = window.pagenow) ? h : "", "shop_order" === i && (a(".wf_pklist_download_document").each(function() {
            var b, c, d, e;
            return d = a(this).closest("tr"), c = a(this).closest("td p"), b = d.find(".wf-pklist-download-tooltip-order-actions"), e = a(this), a(this).remove(), c.append(e), c.find("a.wf_pklist_download_document").tipTip({
                activation: "click",
                content: b.html(),
                keepAlive: !0,
                defaultPosition: "left",
                edgeOffset: 2,
                fadeIn: 10
            })
        }), null != a("select[name=wc_order_action]") && (e = a("select[name=wc_order_action]"), c = e.find("option[value^=wf_pklist_]").detach(), c.length && (b = a("<optgroup>").attr("label").append(c), e.append(b))), a(".wc-reload").on("click", function(b) {
            var c, d;
            return c
        }))
    })
}).call(this);