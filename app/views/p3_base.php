<?php
require_once "../app/models/entities/Folder.php";
require_once "../app/models/entities/Document.php";

# loop on tree to 'double' items (1 value for leftedge and 1 value for leftedge)

# Create UL array tree
$ulTree = [];


# loop on items
#pre-formated string
$stringDocument = '<li class="document"><a href="view?source=%s&id=%d">%s</a></li>';
$stringFolder = '<li class="folder %s"><input type="checkbox" id="%d"><a href="folders?source=%s&id=%d"<label for="%d">%s</label></a>';


foreach ($_SESSION['tree'] as $item){
    
    $interval = ($item->getRightEdge() - $item->getLeftEdge());
    $type = $item->getType();
    $name = $item->getName();
    $id = $item->getId();
    $source = $_SESSION['source'];

    # Folder processing
    if($type == 'folder'){
        # empty folder processing
        if($interval==1){
            $ulTree[$item->getLeftEdge()] = sprintf($stringFolder, 'empty', $id, $source , $id,$id, $name) ."</li>";
        }
        # no empty folder processing
        else{
        # Using left edge to create "<li> folder name" and open subfolders list "<ul>"
        $ulTree[$item->getLeftEdge()] = sprintf($stringFolder, $item->getStatus(), $id, $source , $id,$id, $name) . "<ul>";
        # Right edge to close subfolders list "<\ul>"
        $ulTree[$item->getRightEdge()] = '</li></ul>';
        } 
    }
    else{
    # document processing
        $ulTree[$item->getLeftEdge()] = sprintf($stringDocument, $source , $id,$name);
    }

}

# sort uttree
ksort($ulTree,SORT_NUMERIC) ;


# first ul tag
echo "<ul id='origin'>";

foreach ($ulTree as $line)
{
    echo $line;
}  
echo "</ul>";

?>