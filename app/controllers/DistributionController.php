<?php 
class DistributionController{
    public function autoDistribution(){
        
        echo "Automatic distribution of messages executed.";
        $stock = new StockRepository()->getAll();
            foreach($stock as $item){
                echo "Distributing message for stock: " . $item['name'] . "<br>";
            }

    }
}