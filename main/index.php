<?php

?>

<style>
    body {
        background-color: #4b95bc;
    }

    form {
        width: 750px;
        margin-left: auto;
        margin-right: auto;
        color: white;
        text-align: center;
        font-weight: 900;
        font-size: 100px;
        font-family: Georgia;
        margin-top: 15%;
    }

    textarea {
        width: 400px;
        height: 50px;
        resize: none;
        margin-bottom: 10px;
        font-size: 30px;
    }

    select {
        margin-bottom: 10px;
        width: 400px;
        text-align: center;
    }

    button {
        width: 400px;
    }

    .content {
        color: white;
        text-align: center;
        font-size: 20px;
    }
</style>

<!doctype html>
<html>
    <head>
        <title>Microservices</title>
    </head>
    <body>
        <form>
            <select name="lang_code">
                <option value="all">Selecteer een taal</option>
                <option value="ar-IQ">Arabisch (Iraq)</option>
                <option value="ar-SY">Arabisch (Syria)</option>
                <option value="bg-BG">Bulgarian</option>
                <option value="hi-IN">Hindi</option>
                <option value="ur-PK">Urdu</option>
            </select>
            <br>
            <textarea name="text" required></textarea>
            <br>
            <button id="submit">Translate</button>
        </form>

        <div class="content">

        </div>

        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
        <script>
            /**
             * De functie AjaxFailed handelt de JSON post af wanneer deze foutief opgehaald
             * is. Error message word meegegeven in de console.
             * @param {array} ajax Data opgehaald met de request.
             * @param {bool} exception Excepttie
             * @param {string} errorThrown Opgegooide error
             */
            function AjaxFailed(ajax, exception, errorThrown) {
                "use strict";
                var msg = "Fout bij het maken van een Ajax request:\n\n";
                if (exception) {
                    msg += "Exception: " + errorThrown;
                } else {
                    msg += "Server status:\n" + ajax.status + "\nResponseText: " + ajax.responseText + "\nErrorThrown " + errorThrown;
                }
                alert(msg);
            }

            $(document).ready(function () {
                $('#submit').on('click', function(e){
                    e.preventDefault();
                    $('.content').html('');
                    if($('textarea[name="text"]').val() !== '') {
                        var text = $('textarea[name="text"]').val();
                        var lang_code = $('select[name="lang_code"]').val();

                        var data = JSON.stringify({language:lang_code, search:text});

                        $.ajax({
                                type: "GET",
                                dataType: "json",
                                url: 'http://translation.local',
                                data: data,
                                success: function(result) {
                                    if(result.success === false) {
                                        alert(result.error.message);
                                    } else {
                                        var content = '';

                                        $.each(result.results, function(lang_code, array){
                                            content += '<strong>'+lang_code+'</strong>:';

                                            var count = 1;
                                            $.each(array, function(key, translation){
                                                content += translation;

                                                if(count < array.length) {
                                                    content += ', ';
                                                }

                                                count++;
                                            });

                                            content += '<br>';
                                        });

                                        $('.content').html(content);
                                    }
                                },
                                error: AjaxFailed
                            });
                    } else {
                        alert('Vul het textveld in!');
                    }
                });
            });
        </script>
    </body>
</html>