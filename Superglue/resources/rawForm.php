<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="<?php echo Superglue::resourceUrl('css/SuperGlue.css'); ?>" data-superglue-css="CSS4TextElement">
        <style>
            html {
                height:100%;
                padding:0;
            }
            body {
/*                position: absolute;
                top:0;
                left:0;
                right:0;
                bottom:0;*/
                height:100%;
                padding: 0;
                margin: 0;
                font-family: Dosis;
                /*margin: 50px 0 0 0;*/
            }
            form#form {
                height: 100%;
                width: 100%;
            }
            table#table {
                height: 100%;
                width: 100%;
            }
            #controls {
                position:absolute;
                right:0px;
                width: 30px;
                min-height: 100%;
                z-index: 100;
                
            }
            
/*            pre, code {
                max-width: 100%;
                overflow-x: scroll;
            }*/
            
            .controls td {
                padding: 10px;
                background-color: rgb(236, 237, 238);
            }
            
            .controls td {
                height: 30px;
            }
            
            .controls td div {
                display: inline-block;
            }
            
            button.controls   {
                position: relative;
                cursor: pointer;
                width: 30px;
                height: 30px;
                margin: 0;
                padding: 1px 6px;
                border: none;
                outline: none;
                overflow: visible;
                background-color: rgb(255, 41, 61);
                background-repeat: no-repeat;
                background-size: 30px auto;
                background-position: center;
                -webkit-appearance: button;
            }
            td.content {
                /*height: 100%;*/
                text-align: center;
                
            }
            
            #data {
/*                display: block;
                width: 100%;*/
                height: 100%;
                margin: 0;
                /*overflow: scroll;*/
            }
/*            #data code span:focus {
                 border: 0;
                 text-shadow: none;
                 outline: none;
            }*/
            [contenteditable="true"]:focus {
                border: 0;
                outline: none;
                
            }
        </style>
        
        <!--<link rel="stylesheet" href="http://prismjs.com/themes/prism.css">-->
        <!--<link rel="stylesheet" href="<?php echo Superglue::resourceUrl('bower_components/prism/themes/prism.css'); ?>" data-noprefix>-->
        <!--<link rel="stylesheet" href="<?php echo Superglue::resourceUrl('bower_components/prism/plugins/line-numbers/prism-line-numbers.css'); ?>" data-noprefix>-->
        <link rel="stylesheet" href="<?php echo Superglue::resourceUrl('prismjs/prism.css'); ?>">
    </head>
    <body>
        <!--<form id="form" method="PUT" action="<?php echo Superglue::request()->uri(); ?>">-->
            <table id="table" cellspacing="0" cellpadding="0">
<!--                <colspan>
                    <col width="50%"/>
                    <col width="50%"/>
                </colspan>-->
                <tbody>
                    <tr height="50" class="controls">
                        <td>
                            <div style="margin-right:10%;">
                                <span style="  font-family: Montserrat;font-weight:bold;font-size: 13pt">SUPERGLUE</span> <span>Raw Editor</span>
                            </div>
                            <div>
                                Syntax: 
                                <select>
                                    <option>Auto-guess</option>
                                    <option>HTML</option>
                                    <option>CSS</option>
                                    <option>Javascript</option>
                                    <option>PHP</option>
                                    <option>Perl</option>
                                </select>
                            </div>
                            <div style="float:right;">
                                 <button class="controls" onclick="save();">Save</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="1" class="content" style="max-width:100%;">
                            <div>
                                <pre id="data" class="line-numbers" style="display:inline-block;"><code class="language-markup " contenteditable="true" onkeydown="enter(event);" onkeyup="edited(event);"><?php echo htmlentities(file_get_contents($realPath)); ?></code></pre>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        
        <!--</form>-->
<!--        <div id="controls">
            <button>ff</button>
        </div>-->
<!--        <textarea id="data"><?php echo htmlentities(file_get_contents($realPath)); ?></textarea>-->
<!--<script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>-->

        <!--<script src="<?php echo Superglue::resourceUrl('bower_components/prism/components.js'); ?>"></script>-->

<!--        <script src="<?php echo Superglue::resourceUrl('bower_components/prism/prism.js'); ?>" data-default-language="markup"></script>
        <script src="<?php echo Superglue::resourceUrl('bower_components/prism/plugins/line-numbers/prism-line-numbers.min.js'); ?>"></script>
        <script src="<?php echo Superglue::resourceUrl('bower_components/prism/components.js'); ?>"></script>
        <script src="<?php echo Superglue::resourceUrl('bower_components/prism/code.js'); ?>"></script>-->

        <script src="<?php echo Superglue::resourceUrl('prismjs/prism.js'); ?>"></script>
        <script>
            
            function enter(e){
                var event = e || window.event;
                var unicode=event.keyCode? event.keyCode : event.charCode;
                console.log(unicode);
                switch(unicode){
                    case 9: // tab
                        cancelEvent(event);
                        var sel, range, html;
                        var text = "    ";
                        if (window.getSelection) {
                            sel = window.getSelection();
                            if (sel.getRangeAt && sel.rangeCount) {
                                range = sel.getRangeAt(0);
                                range.deleteContents();
                                range.insertNode( document.createTextNode(text) );
                            }
                        } else if (document.selection && document.selection.createRange) {
                            document.selection.createRange().text = text;
                        }
                        return;
                }
            }
            
            function cancelEvent(event){
                event.cancelBubble = true;
                event.stopPropagation();
                event.preventDefault();
            }
            
            var editTimeout = null;
            var editTimeoutTime = 2;
            function edited(e){
                var event = e || window.event;
                
                if (editTimeout){
                    window.clearTimeout(editTimeout);
                }
                
                var unicode=event.keyCode? event.keyCode : event.charCode;
                
                switch(unicode){
//                    case 9: // tab
//                        cancelEvent(event);
//                        return false;
                    case 13: // enter
//                        window.setTimeout(updateSyntax,100);
                        break;
                    default:
                        editTimeout = window.setTimeout(updateSyntax,editTimeoutTime*1000);       
                }
//                console.log(event,unicode);
                
            }
            function updateSyntax(){
                console.log('updated!');
                Prism.highlightAll();
            }
            
            function save(){
                var data = document.getElementById('data').textContent;
                console.log(data);
            }
        </script>
    </body>
</html>

