<?php
$text = $_POST['text'];
$obj = new Huffman($text);
$obj->freq();

class Huffman{
    public $text;
    public $countLatter = array();
    public $codLatter = array();
    public $weight_oll = 0;

    public function __construct($text) {
        $this->text = $text;
    }
    
    function freq(){
        $len = strlen($this->text);
        for($i=0;$i<$len;$i++){
            $this->setCountLetter((string)$this->text[$i]);
            
        }
        $this->buildTree($this->countLatter);
    }
    
    private function setCountLetter($latter){
        if(isset($this->countLatter[$latter])){
            $count = $this->countLatter[$latter];
            $count++;
            $this->countLatter[$latter] = $count;
            $this->codLatter[$latter] = ''; 
        }else{
            $this->countLatter[$latter] = 1;
            $this->codLatter[$latter] = '';
        } 
    }
    
    private function buildTree($countLatter){
        
        $tree = array();
        $parentIndex = 0;
        
        foreach ($this->countLatter as $key=>$val){
            array_push($tree,array('name'=>$key,'parent'=>'-1'));
        }
             
        do{
            $min1 = $this->searchMin($countLatter);
            $min1Val = $countLatter[$min1];
            unset($countLatter[$min1]);
            $min2 = $this->searchMin($countLatter);
            $min2Val = $countLatter[$min2];
            unset($countLatter[$min2]);
            $countLatter[$min1.$min2] = $min1Val+$min2Val;
            
            array_push($tree,array('name'=>$min1.$min2,'parent'=>'-1'));
            $parentIndex = $this->searchSymbolOnTree($tree,$min1.$min2);
            
            $indexMin1 = $this->searchSymbolOnTree($tree,$min1);
            $indexMin2 = $this->searchSymbolOnTree($tree,$min2);
            
            $tree[$indexMin1]['parent'] = $parentIndex;
            $tree[$indexMin2]['parent'] = $parentIndex;
            
            $tree[$indexMin1]['weight'] = '0';
            $tree[$indexMin2]['weight'] = '1';
            
            $arr1 = str_split($min1);
            for($i=0;$i<count($arr1);$i++){
                $a = '0'.$this->codLatter[$arr1[$i]];
                $this->codLatter[$arr1[$i]]=$a;
            }
            $arr2 = str_split($min2);
            for($i=0;$i<count($arr2);$i++){
                $a = '1'.$this->codLatter[$arr2[$i]];
                $this->codLatter[$arr2[$i]]=$a;
            }
            
        }while(count($countLatter)>1);
        
        foreach ($countLatter as $k=>$v){
           $this->weight_oll = $v; 
        }

         $l = 0; 
         $h = 0;
         foreach($this->countLatter as $k=>$v){
             $l += (((100/$this->weight_oll)*$v)/100)*strlen($this->codLatter[$k]);
             $h += (((100/$this->weight_oll)*$v)/100)*(log(1/(100/$this->weight_oll/100)));
         }
         
        $treeNew = array();
        foreach ($tree as $k=>$v){
            if($v['parent']==-1){
                //echo $k;
                array_push($treeNew,array('name'=>$v['name'],'parent'=>'-1','index'=>0,'weight'=>$v['weight'])); 
                $treeNew = $this ->searchTreeNew($tree, $treeNew, $k,0);
            }
        }
        
        $rating = $treeNew;

        $by = 'parent';
        usort($rating, function($first, $second) use( $by  ) {
            if ($first[$by]>$second[$by]) { return 1; }
            elseif ($first[$by]<$second[$by]) { return -1; }
            return 0;
        });
        
        for($i=0;$i<count($rating);$i+=2){
            if(isset($rating[$i+1])&&isset($rating[$i+2])){
                if($rating[$i+1]['weight']<$rating[$i+2]['weight']){
                    $s1 = $rating[$i+1];
                    $s2 = $rating[$i+2];
                    $rating[$i+1]=$s2;
                    $rating[$i+2]=$s1;
                }
            }
            
        }

        $table='<table><tr><td>symbol</td><td>the number of occurrences</td><td>coding</td></tr>';
        foreach ($this->countLatter as $k=>$v){
            $table.='<tr><td>'.$k.'</td><td>'.$v.'</td><td>'.$this->codLatter[$k].'</td></tr>';
        }
        $table.='</table>';
    echo json_encode(array($rating,$h,$l,$table));   
  }
    
    private function searchTreeNew($tree, $treeNew, $indexSymbolOld, $indexSymbolNew){
        for($i=0;$i<count($tree);$i++){
            if($tree[$i]['parent']==$indexSymbolOld){
               array_push($treeNew,array('name'=>$tree[$i]['name'],'parent'=>$indexSymbolNew,'weight'=>$tree[$i]['weight'])); 
               $indexSymbolNew1 = $this->searchSymbolOnTree($treeNew,$tree[$i]['name']);
               $treeNew[$indexSymbolNew1]['index'] =  $indexSymbolNew1;
               $treeNew = $this->searchTreeNew($tree, $treeNew, $i, $indexSymbolNew1);
            }
        }
        return $treeNew;  
    }

    private function searchMin($countLatter){
        $min='';
        foreach($countLatter as $key=>$value){
            if($min==''){
                $min=$key;
            }else{
                if($countLatter[$min]>$value){
                    $min = $key;
                }
            }
        }
        return $min;
    }
    
    private function searchSymbolOnTree($tree,$symbol){
        $index = 0;
        if(count($tree)>0){
            for($i=0; $i<count($tree);$i++){
                if((string) $tree[$i]['name']== (string)$symbol){
                    $index = $i;
                }
            }
        }
        return $index;
    }
    
}

