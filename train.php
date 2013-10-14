<?php

$stop = "SOLW";
$url = "http://m.metlink.org.nz/stop/" . $stop . "/departures";
$ch = @curl_init();

if($ch){
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/30.0.1599.69 Safari/537.36');

	$content = curl_exec($ch);
	$headers = curl_getinfo($ch);				
			
	curl_close($ch);
			
	if($headers['http_code'] == 200){ 
        	$dom = new DOMDocument(); 
                @$dom->loadHTML($content); 
                $tempDom = new DOMDocument(); 
                
                $xpath = new DOMXPath($dom);
                $site = $xpath->query("/html/body/table[@class='service-list']"); 
                $ann = $xpath->query("//html/body/div[@class='help']/p/text()");                

                foreach ( $site as $item ) { 
                        $tempDom->appendChild($tempDom->importNode($item,true)); 
                }
                $tempDom->saveHTML();
                $trainsXpath = new DOMXPath($tempDom);
                
                $results = array();
                
                $trains = $trainsXpath->query("tr[not(contains(@class, 'rowDivider'))]");

                foreach ($trains as $train) {
                	$newDom = new DOMDocument; 
                        $newDom->appendChild($newDom->importNode($train,true)); 
                        $trainXpath = new DOMXPath( $newDom ); 
                                
                        $train = trim($trainXpath->query("td[2]/a[@class='rt-service-destination ']/text()")->item(0)->nodeValue);
                        $time = trim($trainXpath->query("td[@class='time']/span[@class='actual']/text()")->item(0)->nodeValue);
                               
                                
                        $results[] = array( 
                		'train' => $train,	
                		'time' => $time,         
                        ); 

                }
                $annoucement = trim($ann->item(0)->nodeValue);
                if ($annoucement != "") {
                echo "<h1>Current Announcements</h1>";
                echo $annoucement."<br />";
                }
                
                echo "<h1>Upcoming Trains</h1>";
                echo "<pre>";
                print_r($results);
                echo "</pre>";
        }			
}			

?>
