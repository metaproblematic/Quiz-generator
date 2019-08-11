
    // Test 2
    function saveData(table) {

        var formData = new FormData;
        formData.append("table",table);
        var endpoint = "https://toolbox.injoyonline.com/poll-parser/polls.php";
        var answerArray = [];
        var info = document.querySelectorAll("[data-type=response]");
        for (var i = 0; i < info.length; i++) {
            if (info[i].checked === true) {
                var answer = info[i].value;
                answerArray.push(answer);
            } 
        }
        answerArray = answerArray.join(",");
        formData.append("userResponse",answerArray);
        
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var output = JSON.parse(this.responseText);
            console.log(output);
            document.body.innerHTML = "";
            var possible = [];
            var len = output.length;
            for (var x = 0; x < len; x++){
                var userResponse = output[x]['userResponse'];
                var possibleLength = possible.length;
                if (possibleLength == 0){
                    possible.push(userResponse);
                    // count it
                } else {
                    for (var i = 0; i < len; i++){
                        if (possible[i] != userResponse){
                            possible.push(userResponse);
                        }
                    }
                }
            }

        }
        };
        xhttp.open("POST", endpoint, true);
        xhttp.send(formData);
    }