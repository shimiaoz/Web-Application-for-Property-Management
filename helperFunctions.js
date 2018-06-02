/* Helper JavaScript functions for displaying dynamic webpages */

function sortTable(id, col) {
    // Change icon and determine sort order
    var icon = document.getElementById(id).getElementsByTagName("i")[0];
    var sortBy;
    if (icon.className == "fa fa-chevron-circle-down") {
        icon.className = "fa fa-chevron-circle-up";
        icon.title = "Desc";
        sortBy = "desc";
    }
    else {
        icon.className = "fa fa-chevron-circle-down";
        icon.title = "Asc";
        sortBy = "asc";
    }

    // Sort table by sortBy
    // Modified from https://www.w3schools.com/howto/howto_js_sort_table.asp
    var table = document.getElementById("table");
    var continueSort = true;
    var swap = false;
    var rows;
    while (continueSort) {
        rows = table.getElementsByTagName("tr");
        continueSort = false;
        for (var i = 1; i < rows.length - 1; i++) {
            swap = false;
            var curr = rows[i].getElementsByTagName("td")[col];
            var next = rows[i+1].getElementsByTagName("td")[col];
            if (id == "sort_id") {
                // Entries in the id column are links
                curr = curr.getElementsByTagName("a")[0];
                next = next.getElementsByTagName("a")[0];
            }
            var currData = curr.innerHTML.toLowerCase();
            var nextData = next.innerHTML.toLowerCase();
            if (!isNaN(currData) && !isNaN(nextData)) {
                currData = Number(currData);
                nextData = Number(nextData);
            }
            if ((sortBy == "asc" && currData > nextData) || ((sortBy == "desc" && currData < nextData))) {
                swap = true;
                break;
            }
        }
        if (swap) {
            rows[i].parentNode.insertBefore(rows[i+1], rows[i]);
            continueSort = true;
        }
    }
}

function searchRange(id) {
    var x = document.getElementById(id).value;
    if (x == "Size" || x == "Visits" || x == "Rating" || x == "numProperty") {
        document.getElementById("term-input").innerHTML = "<input class='term range' type='number' step='any' placeholder='From' name='From' required> - <input class='term range' type='number' step='any' placeholder='To' name='To' required>";
    }
    else {
        document.getElementById("term-input").innerHTML = "<input class='term' type='text' placeholder='Search Term' name='SearchTerm'>";
    }
}

function selectRow(idList, type="") {
    var table = document.getElementById('table');

    if (type === "action") {
        var link = document.getElementById(idList[0]).action;
    }

    for(var i = 1; i < table.rows.length; i++) {
        table.rows[i].onclick = function() {
            rows = table.getElementsByTagName("tr")
            for (var j = 1; j < rows.length; j++) {
                rows[j].removeAttribute("id");
            }
            this.id = "selected";
            //console.log(idList);
            if (type === "action") {    //idList.length == 1
                var idLink = this.cells[0].getElementsByTagName("a")[0];
                document.getElementById(idList[0]).action = link + idLink.innerHTML;
                document.getElementById("invisible_field").value = idLink.innerHTML;
            }
            else {
                for (var i = 0; i < idList.length; i++) {
                    document.getElementById(idList[i]).value = this.cells[0].innerHTML;
                }
            }
        };
    }
}
