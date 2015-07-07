<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="<?php echo Superglue::resourceUrl('css/SuperGlue.css'); ?>" data-superglue-css="CSS4TextElement">
        <link rel="stylesheet" href="<?php echo Superglue::resourceUrl('bower_components/codemirror/lib/codemirror.css') ?>">
        <link rel="stylesheet" href="<?php echo Superglue::resourceUrl("bower_components/codemirror/theme/{$CodeMirror['theme']}.css") ?>">
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
            /*td.content {*/
                /*height: 100%;*/
                /*text-align: center;*/
                /**/
            /*}*/
            td.error {
                text-align: center;
            }
            td.error .title {
                margin: 20px 0;
                color: red;
                font-size: 40px;
                font-decoration: none;
            }
            #editor {
                height: 100%;
                vertical-align: top;
                text-align: left;
            }
            .CodeMirror {
                height: 100%;
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
            /*[contenteditable="true"]:focus {*/
                /*border: 0;*/
                /*outline: none;*/
                /**/
            /*}*/
        </style>
        
        <!--<link rel="stylesheet" href="http://prismjs.com/themes/prism.css">-->
        <!--<link rel="stylesheet" href="<?php echo Superglue::resourceUrl('bower_components/prism/themes/prism.css'); ?>" data-noprefix>-->
        <!--<link rel="stylesheet" href="<?php echo Superglue::resourceUrl('bower_components/prism/plugins/line-numbers/prism-line-numbers.css'); ?>" data-noprefix>-->
<!--        <link rel="stylesheet" href="--><?php //echo Superglue::resourceUrl('prismjs/prism.css'); ?><!--">-->

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
                        <td colspan="1" class="content">
                            <div id="editor"></div>
                        </td>
                    </tr>
                </tbody>
            </table>


        <script src="<?php echo Superglue::resourceUrl('bower_components/codemirror/lib/codemirror.js'); ?>"></script>
        <script src="<?php echo Superglue::resourceUrl('bower_components/nanoajax/nanoajax.min.js'); ?>"></script>
        <script>
//            var editorConfig
            var editor = null;

            function requestFile(uri){
                nanoajax.ajax("/raw"+uri+"?data=1",function(code,data){
//                    console.log(code,data);
                    if (code == 200){
                        editor = CodeMirror(document.getElementById('editor'),{
                            value: data,
                            mode: "javascript",
                            theme: "<?php echo $CodeMirror['theme']; ?>",
                            lineNumbers: true
                        })
                    } else {

                    }
                });
            }

            requestFile("<?php echo $path; ?>");

            function selectLanguage(lang){
            }
            function save(){
                console.log(editor.getValue());

                editor.markClean();
            }
        </script>
    </body>
</html>

