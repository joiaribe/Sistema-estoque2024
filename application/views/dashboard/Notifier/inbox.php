<!-- echo out the system feedback (error and success messages) -->
<?php
$this->renderFeedbackMessages();

use Dashboard\menu as menu;
use Dashboard\breadcrumb as breadcrumb;
use Dashboard\sidebar as sidebar;

// load menu
new menu($filename);
// load page notifier
new inboxModel('loaded'); // used param for load class only here :P
// load sidebar
new sidebar();
?>
<script>
    var checked = false;
    function checkedAll() {
        var aa = document.getElementsByName("checkboxes");
        checked = document.getElementById('select_all').checked;

        for (var i = 0; i < aa.length; i++)
        {
            aa[i].checked = checked;
        }
    }


    $(function () {
        function highlightText(text, $node) {
            var searchText = $.trim(text).toLowerCase(), currentNode = $node.get(0).firstChild, matchIndex, newTextNode, newSpanNode;
            while ((matchIndex = currentNode.data.toLowerCase().indexOf(searchText)) >= 0) {
                newTextNode = currentNode.splitText(matchIndex);
                currentNode = newTextNode.splitText(searchText.length);
                newSpanNode = document.createElement("span");
                newSpanNode.className = "highlight";
                currentNode.parentNode.insertBefore(newSpanNode, currentNode);
                newSpanNode.appendChild(newTextNode);
            }
        }
        $("#autocomplete").autocomplete({
            source: data_autocomplete
        }).data("ui-autocomplete")._renderItem = function (ul, item) {
            var $a = $("<a></a>").text(item.label);
            highlightText(this.term, $a);
            return $("<li></li>").append($a).appendTo(ul);
        };
    });
    
    function Search() {
        var form = document.getElementById("form_search");
        var field = document.getElementById("id_search");
        form.action = form.action + encodeURIComponent(field.value).replace(/%20/g, "+").replace(/%2/g, "");
        form.submit();
    }
</script>