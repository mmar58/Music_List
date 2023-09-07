<html>
<head>
    <title>
        Music List
    </title>
    <link rel="stylesheet" href="css/material-components-web.min.css">
    <link rel="stylesheet" href="css/dataTables.material.min.css">
    <script src="js/jquery-3.5.1.js"></script>
    <script src="js/jquery.dataTables.min.js"></script>
    <style>
        #musicTable tr:hover{
            background-color: rgba(24, 27, 40, 0.24);
        }
    </style>
</head>
<body>
<h1 style="text-align: center">My music list</h1>
<!--Skip words Ui start-->
<button id="addSkipWordsButton" onclick="showSkipWordsDiv()" style="position: absolute;right: 2%">Add skip words for search</button>
<div id="SkipWordsDiv" style="position: absolute;left: 50%;top: 50%;transform: translate(-50%, -50%);background: rebeccapurple;padding: 20px;display: none">
    <button onclick="hideSkipWordsDiv()" style="position: relative;right: -79%"><img src="icons/close-button.png"></button>
    <h2 style="
    padding: 0;
    margin: 0;
    margin-bottom: 12px;
">Input the word</h2>
    <input id="skipWordsInput" type="text"><br>
    <button onclick="addSkipWords(skipWordsInput.value)">Submit</button>
</div>
<!--Skip words Ui end-->
<span id="matchingNotice"></span>
<table id="matchWordsTable" style="width: 100%; display: none">
    <thead>
    <tr><th>Matched Words</th><th>Matched Count</th></tr>
    </thead>
    <tbody id="matchWordsTableBody">

    </tbody>
</table>
<table id="musicTable" style="width: 100%">
    <thead>
    <tr><th>Name</th><th>Modified Date</th><th>Size</th></tr>
    </thead>
    <tbody >
    <?php $music_path="H:\Video songs"; $tableData="";

    function getSize($cursize){
        $curSizeStep=0;
        $sizeStringList=["Byte","KB","MB","GB"];
        while ($curSizeStep<count($sizeStringList)&&$cursize>1024){
            $cursize=$cursize/1024;
            $curSizeStep++;
        }
        return round($cursize,2)." ".$sizeStringList[$curSizeStep];
    }
    ?>
<!--    Introducing file names variables in php -->
    <?php
        $fileslists="";
    ?>
    <?php foreach ($files = array_diff(scandir($music_path), array('.', '..')) as $music_name){
        $fileslists.='"'.$music_name.'",';
        $tableData.="<tr><td>.$music_name.</td><td>".date("d F Y H:i:s.",filectime($music_path."/".$music_name)).
            "</td><td>".getSize(filesize($music_path."/".$music_name))."</td></tr>";
    };
    echo  $tableData;
    ?>
    </tbody>
</table>

</body>
<!--Skip Words UI Script-->
<script>
    var noticeTimeOut
    var skipWordsDiv=document.getElementById("SkipWordsDiv"),
        skipWordsShowButton=document.getElementById("addSkipWordsButton"),
        skipWordsInput=document.getElementById("skipWordsInput")
    function showSkipWordsDiv(){
        skipWordsDiv.style.display=""
        skipWordsShowButton.style.display="none"
    }
    function hideSkipWordsDiv(){
        skipWordsDiv.style.display="none"
        skipWordsShowButton.style.display=""
    }
    function addSkipWords(word){
        var previousWords=localStorage.getItem("skipWords")
        if(previousWords==null){
            previousWords=""
            localStorage.setItem("skipWords",previousWords+word)
        }
        else{
            localStorage.setItem("skipWords",previousWords+"###"+word)
        }
        showNotice("Added skip word - "+word)
    }
    function showNotice(text){
        if(noticeTimeOut!=null){
            clearTimeout(noticeTimeOut)
        }
        matchingNoticeSpan.innerText=text
        matchingNoticeSpan.style.display=""
        noticeTimeOut=setTimeout(function (){
            matchingNoticeSpan.style.display="none"
        },2000)
        console.log(text)
    }
</script>
<!--Searching duplicate words in the music list-->
<script>
//    You can add words in the list to skip them
    var previousSearchedWords=[]
    var previousWords=localStorage.getItem("skipWords")
    if(previousWords!=null&&previousWords!=""){
        let previousWordsData=previousWords.split("###")
        previousWordsData.forEach(word=>{
            previousSearchedWords.push(word)
        })
    }
    var matchedWordOutPutTableBody=document.getElementById("matchWordsTableBody")
    var fileCounter=0
    var matchCount=0
    var matchingNoticeSpan=document.getElementById("matchingNotice")
    var musiclist=[<?php echo $fileslists?>]
    function ifThisWordUsedPreviously(word){
        console.log(word)
        for(var j=0;j<previousSearchedWords.length;j++){
            if(previousSearchedWords[j]==word){
                return true
                break
            }
        }
        return false
    }
    function filesNameCompare(){
        matchingNoticeSpan.innerText="Done "+fileCounter+" of "+musiclist.length
        var data=musiclist[fileCounter].split(" ")
        for(var i=0;i<data.length;i++){
            matchCount=0
            if(i+1<data.length){
                var searchWord=data[i]+" "+data[i+1]
                if(!ifThisWordUsedPreviously(searchWord))
                {
                    musiclist.forEach(name=>{
                        if(name.split(searchWord).length>1){
                            matchCount++
                        }
                    })
                    if(matchCount>1){
                        matchedWordOutPutTableBody.innerHTML+="<tr><td>"+searchWord+"</td><td> "+matchCount+"</td></tr> "
                    }
                    previousSearchedWords.push(searchWord)
                }
            }
        }
        fileCounter++
        if(fileCounter<musiclist.length){
            setTimeout(filesNameCompare,1)
        }
        else{
            matchingNoticeSpan.style.display="none"
            document.getElementById("matchWordsTable").style.display=""
            $('#matchWordsTable').DataTable(
                {
                    lengthMenu:[20,30,40,50],
                    autoWidth: false,
                    columnDefs: [
                        {
                            targets: ['_all'],
                            className: 'mdc-data-table__cell',
                        },
                    ],
                    order:[[ 1, "desc" ]]
                }
            );
        }
    }
    filesNameCompare()
</script>
<script>
    $(document).ready(function () {
        $('#musicTable').DataTable(
            {
                lengthMenu:[50,100],
                autoWidth: false,
                columnDefs: [
                    {
                        targets: ['_all'],
                        className: 'mdc-data-table__cell',
                    },
                ],
            }
        );

    });
</script>
</html>