/* Helper JavaScript functions for displaying dynamic webpages*/

function sortTable(id, col) {
    // Change icon and determine sort order
    var icon = document.getElementById(id).getElementsByTagName("i")[0];
    var sortBy;
    if (icon.className == "fa fa-chevron-circle-down") {
        icon.className = "fa fa-chevron-circle-up";
        sortBy = "desc";
    }
    else {
        icon.className = "fa fa-chevron-circle-down";
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

function searchRange() {
    var x = document.getElementById('visitorSearch').value;
    if (x == "Size" || x == "Visits" || x == "Rating") {
        document.getElementById("term-input").innerHTML = "<input class='term range' type='number' step='any' placeholder='From' name='From' required> - <input class='term range' type='number' step='any' placeholder='To' name='To' required>";
    } else {
        document.getElementById("term-input").innerHTML = "<input class='term' type='text' placeholder='Search Term' name='SearchTerm'>";
    }
}