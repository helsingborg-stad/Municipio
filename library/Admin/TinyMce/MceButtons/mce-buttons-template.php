<?php require_once('../View/header.php'); ?>
            <div class="grid">
                <div class="grid-xs-12">
                    <div id="preview">
                        <div>
                            <a class="btn">Button text</a>
                        </div>
                    </div>
                </div>
                <div class="grid-xs-12">
                    <form id="btnColor" class="hide-radio-input">
                        <input id="btn-1" type="radio" name="gender" value="" checked>
                        <label for="btn-1"><span class="btn"></span> </label>
                        <input id="btn-2" type="radio" name="gender" value="btn-light">
                        <label for="btn-2"><span class="btn btn-light"></span></label>
                        <input id="btn-3" type="radio" name="gender" value="btn-contrasted">
                        <label for="btn-3"><span class="btn btn-contrasted"></span></label>
                        <input id="btn-4" type="radio" name="gender" value="btn-theme-first">
                        <label for="btn-4"><span class="btn btn-theme-first"></span></label>
                        <input id="btn-5" type="radio" name="gender" value="btn-theme-second">
                        <label for="btn-5"><span class="btn btn-theme-second"></span></label>
                        <input id="btn-6" type="radio" name="gender" value="btn-theme-third">
                        <label for="btn-6"><span class="btn btn-theme-third"></span></label>
                        <input id="btn-7" type="radio" name="gender" value="btn-theme-fourth">
                        <label for="btn-7"><span class="btn btn-theme-fourth"></span></label>
                        <input id="btn-8" type="radio" name="gender" value="btn-theme-fifth">
                        <label for="btn-8"><span class="btn btn-theme-fifth"></span></label>
                    </form>
                </div>
            </div>

            <br>

            <div class="grid">
                <div class="grid-xs-6">
                    <input id="btnText" type="text" value="Button text" placeholder="Button text">
                </div>
                <div class="grid-xs-6">
                    <input id="btnLink" type="text" value="https://helsingborg.se/" placeholder="Button URL">
                </div>
            </div>

            <br>

            <div class="grid">
                <div class="grid-xs-6">
                    <form id="btnType" class="button-type">
                        <h6>Button style</h6>
                        <div>
                            <input id="btn-default" type="radio" name="gender" value="btn" checked>
                            <label for="btn-default">Default button</label>
                        </div>
                        <div>
                            <input id="btn-outline" type="radio" name="gender" value="btn btn-outline">
                            <label for="btn-outline">Outline button</label>
                        </div>
                    </form>
                </div>
                <div class="grid-xs-6">
                    <form id="btnSize" class="button-size">
                            <h6>Button size</h6>
                            <div>
                                <input id="btn-md" type="radio" name="gender" value="" checked>
                                <label for="btn-md">Default</label>
                            </div>
                            <div>
                                <input id="btn-lg" type="radio" name="gender" value="btn-lg">
                                <label for="btn-lg">Large</label>
                                </div>
                            <div>
                                <input id="btn-sm" type="radio" name="gender" value="btn-sm">
                                <label for="btn-sm">Small</label>
                            </div>
                    </form>
                </div>
            </div>
            <style>
                form#btnColor span {
                    width: 100%;
                    border-radius: 0;
                    height: 50px;
                    margin: 1px;
                }

                form#btnColor {
                    text-align: center;
                    display: flex;
                    align-content: stretch;
                }

                form#btnColor label {
                    display: flex;
                    flex-grow: 1;
                }

                #preview {
                    justify-content: center;
                    display: flex;
                    height: 100px;
                    align-items: center;
                    text-align: center;
                }
            </style>
            <script>
                $(document).ready(function () {
                    if ( typeof top.tinymce !== 'undefined') {
                        //Standard WP editor
                        $('head').append('<link rel="stylesheet" type="text/css" href="' + top.tinymce.activeEditor.windowManager.getParams().stylesSheet + '">');
                    } else {
                        //Modularity iFrame editor
                        $('head').append('<link rel="stylesheet" type="text/css" href="' + window.parent.tinymce.activeEditor.windowManager.getParams().stylesSheet + '">');
                    }
                    $( '#btnText' ).keyup(function() {
                        $("#preview a").html($('#btnText').val());
                    });
                    $('input[type="radio"]').click(function () {
                        var btnType = $('#btnType input:checked').val();
                        var btnColor = $('#btnColor input:checked').val();
                        var btnSize = $('#btnSize input:checked').val();
                        $('#preview a').removeClass();
                        $('#preview a').addClass(btnType + ' ' + btnColor + ' ' + btnSize);
                    });
                });
            </script>
<?php require_once('../View/footer.php'); ?>
