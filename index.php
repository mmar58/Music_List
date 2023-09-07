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
<span id="duplicatefiles" ></span>
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
<!--Searching duplicate words in the music list-->
<script>
    var previousSearchedWords=[]
    var outputDiv=document.getElementById("duplicatefiles")
    var fileCounter=0
    var matchCount=0
    var musiclist=[<?php echo $fileslists?>]
    function ifThisWordUsedPreviously(word){
        for(var i=0;i<previousSearchedWords;i++){
            if(previousSearchedWords[i]==word){
                return true
                break
            }
        }
        return false
    }
    function filesNameCompare(){
        var data=musiclist[fileCounter].split(" ")
        for(var i=0;i<data.length;i++){
            matchCount=0
            if(!ifThisWordUsedPreviously(data[i]))
            {
                musiclist.forEach(name=>{
                    if(i+1<data.length){
                        if(name.split(data[i]+" "+data[i+1]).length>1){
                            matchCount++
                        }
                    }

                })
                if(matchCount>1){
                    outputDiv.innerText+=data[i]+" "+data[i+1]+"("+matchCount+") "
                }
                previousSearchedWords.push(data)
            }
        }
        fileCounter++
        if(fileCounter<musiclist.length){
            setTimeout(filesNameCompare,5)
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