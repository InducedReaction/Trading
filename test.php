<?php
include_once'connect.php';
include_once'maketrade.php';


//buytoopen("LIMIT","0.01",1,"AMD_052424C200",$code1);
//buytoopen("LIMIT","0.01",1,"AMD_052424C200",$code1);
//selltoclose("LIMIT","0.01",1,"AMD_052424C200",$code1);
//selltoopen("LIMIT","0.01",1,"AMD_052424C200",$code1);
//buytoclose("LIMIT","0.01",1,"AMD_052424C200",$code1);

etrade_buy("LIMIT","0.90",1,"SPCE");
//buy("LIMIT","0.90",1,"SPCE",$code1);
//sell("LIMIT","1.00",1,"SPCE",$code1);
//sellshort("LIMIT","1.00",1,"SPCE",$code1);
//buytocover("LIMIT","1.00",1,"SPCE",$code1);
                            
//buytoopen("LIMIT","10.05",1,"AAPL  240524C00200000",$code1);

?>