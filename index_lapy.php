<!DOCTYPE html>
<?php
//include("..\..\W3C_lib\LIB_http.php");
//include("..\..\W3C_lib\LIB_parse.php");
//include("..\config\config.php");
set_time_limit(6000000); 
//$link = mysql_connect('140.112.233.123', 'root', 'lapy110');
//$link = mysql_connect('127.0.0.1', 'root', 'lapy110');
$link = mysql_connect('127.0.0.1', 'websysS15GB4', 'websysS15GB4!!');
mysql_query("SET NAMES utf8");
mysql_select_db("websysS15GB4",$link);

$TICKER = array_key_exists('TICKER',$_GET)?$_GET['TICKER']:"AMZN:US";
$query = "SELECT * FROM  `testcount` WHERE MAJOR_TICKER = '$TICKER';";
$result =mysql_query($query,$link);

if( $result && mysql_num_rows($result)==0 )
{
	echo "TICKER <b>\" $TICKER\"</b> is not found.</br>";
	return;
}

$query = "SELECT * FROM  `companyb4_unique` WHERE MAJOR_TICKER = '".$TICKER."';";
$result = mysql_query($query);     
$row = mysql_fetch_array($result);
$CompanyName = $row['CompanyName'];
?>

<html>
    <head>
        <meta charset="UTF-8">
        <title>AdminLTE | Dashboard</title>
		<link rel="stylesheet" href="style.css" media="screen" />
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <!-- Morris chart -->
        <link href="css/morris/morris.css" rel="stylesheet" type="text/css" />
        <!-- jvectormap -->
        <link href="css/jvectormap/jquery-jvectormap-1.2.2.css" rel="stylesheet" type="text/css" />
        <!-- fullCalendar -->
        <link href="css/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css" />
        <!-- Daterange picker -->
        <link href="css/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
        <!-- bootstrap wysihtml5 - text editor -->
        <link href="css/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="css/AdminLTE.css" rel="stylesheet" type="text/css" />
		
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
		<script src="http://d3js.org/d3.v3.min.js"></script>
		<script src="dndTree.js"></script>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
		<script type="text/javascript">
			var change=window.innerWidth;
			window.onresize=function(){
				if(window.innerWidth!=change)
					location.reload();
			};
			/*if(change==1)
			{
					change=-1;
					location.reload();
			}*/
			google.load("visualization", "1", {packages:["corechart"]});
      		google.load('visualization', '1', {'packages':['motionchart']});
      		google.setOnLoadCallback(drawCharts);
			
			function drawCharts()
			{
				drawChartSI();
				drawChart6();
				drawChart2();
				drawChartPie();
			}
			
			function drawChartSI(  ) {//draw industry
				<?php


				$TICKER = array_key_exists('TICKER',$_GET)?$_GET['TICKER']:"AAPL:US";

				$query = "SELECT * FROM  `testcount` WHERE MAJOR_TICKER = '$TICKER';";
				$result = mysql_query($query,$link);
				$row = mysql_fetch_array($result);

				$RELATED_TICKERS = json_decode($row['RELATED_COMPANIES_JSON'],true);
				$CORELATION = json_decode($row['CORELATION'],true);

				echo "var data = google.visualization.arrayToDataTable([\n";
				echo "['TICKER', 'Sub-Industrial Categories', 'Corelation', 'Sub-Industrial Categories','Exposure'],\n";

				$industry_cate = array();
				$industry_key = array();

				$NUMS_OF_RELATED_COMPANY = 0;
				for($i=1;$i<=9;$i++){

					$query = "SELECT MAJOR_TICKER,INDUSTRY,SECTOR,SUB_INDUSTRY,CompanyName FROM  `companyb4_unique` WHERE MAJOR_TICKER = '".$RELATED_TICKERS[$i]['symbol']."';";
					$result = mysql_query($query);

					if(mysql_num_rows($result) == 0)
					{

					}
					else if(mysql_num_rows($result) == 1)
					{	
						$NUMS_OF_RELATED_COMPANY++;
						$row = mysql_fetch_array($result);

						$key = array_search($row['SUB_INDUSTRY'], $industry_cate);
						if($key === FALSE)
						{
							$industry_cate[] = $row['SUB_INDUSTRY'];
							//print_r($industry_cate);
							$key = array_search($row['SUB_INDUSTRY'], $industry_cate);
							//print_r($industry_cate);
							//print_r($CORELATION);
						}
						$industry_key[] = $key;

						if($RELATED_TICKERS[$i]['counts']/$RELATED_TICKERS[0]['counts'] ==1)
							$modified_corelation = 1;
						else if ($RELATED_TICKERS[$i]['counts'] == 0)
							$modified_corelation = 0;
						else{

							$modified_corelation = 1/( 1+ exp(-1/(1-$RELATED_TICKERS[$i]['counts']/$RELATED_TICKERS[0]['counts'])));
							$modified_corelation = $RELATED_TICKERS[$i]['counts']/$RELATED_TICKERS[0]['counts'];
						}

						//echo "['".$RELATED_TICKERS[$i]['symbol']."',".($key+2).",".(100*$modified_corelation).",'$row[SUB_INDUSTRY]',".($RELATED_TICKERS[$i]['counts'])."]";
						//echo "['".$row['CompanyName'].'('.$RELATED_TICKERS[$i]['symbol'].")',".($key+2).",".(100*$modified_corelation).",'$row[SUB_INDUSTRY]',".($RELATED_TICKERS[$i]['counts'])."]";
						echo "['".$row['CompanyName']."(".$row['MAJOR_TICKER'].")"."',".($key+1).",".(100*$modified_corelation).",'$row[SUB_INDUSTRY]',".($RELATED_TICKERS[$i]['counts'])."]";

						if($i<9)
							echo ",\n";
					}
				}
				echo "\n]);\n";

				?>

					var options = {
				  	title: 'Correlation',
				  	hAxis: {title: 'Industrial Categories Index'},
				  	vAxis: {title: 'Corelation with <?php echo $TICKER; ?> (%) '},
				  	bubble: {textStyle: {fontSize: 11}}
				};

				var chart = new google.visualization.BubbleChart(document.getElementById('chart_div'));
				chart.draw(data, options);
			}

			function drawChart6()
			{
        		var data = google.visualization.arrayToDataTable([    
         		<?php
				echo "['Month', '$row[SECTOR]', '$row[INDUSTRY]', '$row[SUB_INDUSTRY]', '$CompanyName'],\n";   
				$c =0;  
				for($y=2011;$y<=2013;$y++)
				{

					for($m=1;$m<=12;$m++)
					{
						$query2 = "SELECT SUM(`$m`) FROM news_info_".($y)." WHERE SECTOR='$row[SECTOR]';";//sector
						$query3 = "SELECT SUM(`$m`) FROM news_info_".($y)." WHERE INDUSTRY='$row[INDUSTRY]';";//industry
						$query4 = "SELECT SUM(`$m`) FROM news_info_".($y)." WHERE SUB_INDUSTRY='$row[SUB_INDUSTRY]';";//sub-industry
						//echo $query2."\n";
						//$query5 = "SELECT * FROM `news_".($y).(floor($m/10)).($m%10)."` WHERE `RELTEAD_TICKERS_JSON` LIKE '%".$TICKER."%';";
						//echo $query5;

						$row2 = mysql_fetch_array(mysql_query($query2));
						$row3 = mysql_fetch_array(mysql_query($query3));
						$row4 = mysql_fetch_array(mysql_query($query4));
						//$result5 = mysql_query($query5);

						echo "['".($y)."-".(floor($m/10)).($m%10)."',".($row2["SUM(`$m`)"]).",".($row3["SUM(`$m`)"]).",".($row4["SUM(`$m`)"]).",".($CORELATION[0]['CORELATED_NUMBER'][$c++])."]";

						if($y != 2013 || $m !=12)
							echo ",\n";
					}
				}
				echo "]);";
				?>

				var options = {
				  title: 'News Counts of SECTOR, INDUSTRY and SUB-INDUSTRY belonging to <?php echo $TICKER;?>',
				  hAxis: {title: 'Month',  titleTextStyle: {color: '#333'}},
				  vAxis: {minValue: 0}
				};

				var chart = new google.visualization.AreaChart(document.getElementById('chart_div6'));
				chart.draw(data, options);
		  	}
			
			google.load("visualization", "1", {packages:["corechart"]});
			google.setOnLoadCallback(drawChart);
				
			function drawChartPie() {
				var data = google.visualization.arrayToDataTable([
				<?php

				$query3 = "select distinct(SUB_INDUSTRY) from companyb4_unique where INDUSTRY ='$row[INDUSTRY]';";
				$result3 =mysql_query($query3,$link);
				$ii=0;
				echo "['SUB_INDUSTRY','# of News'],\n";
				for($i=0;$i<mysql_num_rows($result3);$i++)
				{
					$row3 = mysql_fetch_array($result3);
					$query  = "SELECT * FROM `news_info_".(2013)."` WHERE SUB_INDUSTRY = '$row3[SUB_INDUSTRY]';";

					//echo $query;
					$result = mysql_query($query);								
					$row2 = mysql_fetch_array($result);

					if($row3['SUB_INDUSTRY'] == $row['SUB_INDUSTRY'])
						$ii=$i;

					$S = 0;
					for($j=1;$j<=12;$j++)
						$S+= $row2[strval($j)];

					echo "['$row2[SUB_INDUSTRY]',$S]";
					if($i<mysql_num_rows($result3)-1)
						echo ",\n";
				}
							echo "]);";

				?>	
				var options = {
				  title: 'News Distribution in '+'<?php echo $row['INDUSTRY'];?> INDUSTRY',
				  pieHole: 0.4,
				  slices: {  <?php echo $ii;?>: {offset: 0.2},

				  },
				};

				var chart = new google.visualization.PieChart(document.getElementById('chart_div5'));
				chart.draw(data, options);
			}
					
					
			function drawChart2() 
			{
				var data = new google.visualization.DataTable();
				data.addColumn('string', 'TICKER');
				data.addColumn('date', 'Date');
				data.addColumn('number', 'Cate index');
				data.addColumn('number', 'Corelation');
				data.addColumn('string', 'Cate');

				<?php
				echo "data.addRows([  \n";

				for($i=1;$i<=5;$i++)
				{
					$key = $industry_key[$i];
					for($j=0; $j <= 35 ; $j ++ )
					{
						echo "['".$RELATED_TICKERS[$i]['symbol']."',new Date (".(2011+($j/12)).",".(($j)%12).",0),".($key).",".($CORELATION[$i]['COEF'][$j]).",'".$industry_cate[$key]."']";

						if($i != 5 || $j != 35)
							echo ",\n";
					}		
				}

				echo "]);";	
				?>

				var chart = new google.visualization.MotionChart(document.getElementById('chart_div2'));
				chart.draw(data, {width: 500, height:500});
			}		
		</script>
    </head>
    <body class="skin-blue">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="index.html" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
                AdminLTE
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
                    <ul class="nav navbar-nav">
                        <!-- Messages: style can be found in dropdown.less-->
						<!--
                        <li class="dropdown messages-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-envelope"></i>
                                <span class="label label-success">4</span>
                            </a>
							<!--
                            <ul class="dropdown-menu">
                                <li class="header">You have 4 messages</li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
							<!--
                                    <ul class="menu">
                                        <li><!-- start message -->
							<!--
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="img/Apple-Logo.png" class="img-circle" alt="User Image"/>
                                                </div>
                                                <h4>
                                                    Support Team
                                                    <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li><!-- end message -->
							<!--
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="img/avatar2.png" class="img-circle" alt="user image"/>
                                                </div>
                                                <h4>
                                                    AdminLTE Design Team
                                                    <small><i class="fa fa-clock-o"></i> 2 hours</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="img/avatar.png" class="img-circle" alt="user image"/>
                                                </div>
                                                <h4>
                                                    Developers
                                                    <small><i class="fa fa-clock-o"></i> Today</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="img/avatar2.png" class="img-circle" alt="user image"/>
                                                </div>
                                                <h4>
                                                    Sales Department
                                                    <small><i class="fa fa-clock-o"></i> Yesterday</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <div class="pull-left">
                                                    <img src="img/avatar.png" class="img-circle" alt="user image"/>
                                                </div>
                                                <h4>
                                                    Reviewers
                                                    <small><i class="fa fa-clock-o"></i> 2 days</small>
                                                </h4>
                                                <p>Why not buy a new awesome theme?</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="footer"><a href="#">See All Messages</a></li>
                            </ul>
                        </li>
                        <!-- Notifications: style can be found in dropdown.less -->
						<!--
                        <li class="dropdown notifications-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-warning"></i>
                                <span class="label label-warning">10</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 10 notifications</li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
						<!--
                                    <ul class="menu">
                                        <li>
                                            <a href="#">
                                                <i class="ion ion-ios7-people info"></i> 5 new members joined today
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="fa fa-warning danger"></i> Very long description here that may not fit into the page and may cause design problems
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="fa fa-users warning"></i> 5 new members joined
                                            </a>
                                        </li>

                                        <li>
                                            <a href="#">
                                                <i class="ion ion-ios7-cart success"></i> 25 sales made
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">
                                                <i class="ion ion-ios7-person danger"></i> You changed your username
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <li class="footer"><a href="#">View all</a></li>
                            </ul>
                        </li>
                        <!-- Tasks: style can be found in dropdown.less -->
						<!--
                        <li class="dropdown tasks-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-tasks"></i>
                                <span class="label label-danger">9</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li class="header">You have 9 tasks</li>
                                <li>
                                    <!-- inner menu: contains the actual data -->
						<!--
                                    <ul class="menu">
                                        <li><!-- Task item -->
						<!--
                                            <a href="#">
                                                <h3>
                                                    Design some buttons
                                                    <small class="pull-right">20%</small>
                                                </h3>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">20% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li><!-- end task item -->
						<!--
                                        <li><!-- Task item -->
						<!--
                                            <a href="#">
                                                <h3>
                                                    Create a nice theme
                                                    <small class="pull-right">40%</small>
                                                </h3>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">40% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li><!-- end task item -->
						<!--
                                        <li><!-- Task item -->
						<!--
                                            <a href="#">
                                                <h3>
                                                    Some task I need to do
                                                    <small class="pull-right">60%</small>
                                                </h3>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">60% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li><!-- end task item -->
						<!--
                                        <li><!-- Task item -->
						<!--
                                            <a href="#">
                                                <h3>
                                                    Make beautiful transitions
                                                    <small class="pull-right">80%</small>
                                                </h3>
                                                <div class="progress xs">
                                                    <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                                        <span class="sr-only">80% Complete</span>
                                                    </div>
                                                </div>
                                            </a>
                                        </li><!-- end task item -->
						<!--
                                    </ul>
                                </li>
                                <li class="footer">
                                    <a href="#">View all tasks</a>
                                </li>
                            </ul>
                        </li>
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-user"></i>
                                <span>Jane Doe <i class="caret"></i></span>
                            </a>
                            <ul class="dropdown-menu">
                                <!-- User image -->
                                <li class="user-header bg-light-blue">
                                    <img src="img/Apple-Logo.png" class="img-circle" alt="User Image" />
                                    <p>
                                        Jane Doe - Web Developer
                                        <small>Member since Nov. 2012</small>
                                    </p>
                                </li>
                                <!-- Menu Body -->
                                <li class="user-body">
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Account Details</a>
                                    </div>
									
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Saved Searches</a>
                                    </div>
									
                                    <div class="col-xs-4 text-center">
                                        <a href="#">Profile</a>
                                    </div> 
                                </li>
                                <!-- Menu Footer-->
                                <li class="user-footer">
                                    <div class="pull-left">
										<!--
                                        <a href="#" class="btn btn-default btn-flat">Profile</a>
                                    </div>
                                    <div class="pull-right">  
										-->
                                        <a href="#" class="btn btn-default btn-flat">Sign out</a>
                                    </div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="img/Apple-Logo.png" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
                            <p><?php echo $CompanyName."(".$TICKER.")"; ?></p>

                            <a href="#"><i class="fa fa-circle text-success"></i>Market Close</a>
                        </div>
                    </div>
                    <!-- search form -->
                    <form action="#" method="get" class="sidebar-form">
                        <div class="input-group">
                            <input type="text" name="TICKER" class="form-control" method="get" action="index_lapy.php" placeholder="Search Ticker..."/>
                            <span class="input-group-btn">
                                <button type='submit' name='seach' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                            </span>
                        </div>
                    </form>
                    <!-- /.search form -->
                    <!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <li class="active">
                            <a href="index_lapy.php">
                                <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                            </a>
                        </li>
						<li>
							<a href="news.php?TICKER=<?php echo $TICKER;?>">
								<i class="fa fa-tasks"></i> <span>News</span>
							</a>
						</li>
						<!--
                        <li>
							
                            <a href="pages/widgets.html">
                                <i class="fa fa-th"></i> <span>Widgets</span> <small class="badge pull-right bg-green">new</small>
                            </a>
                        </li>
						-->
						<li>
                            <a href="pages/calendar.html">
                                <i class="fa fa-calendar"></i> <span>Calendar</span>
                                <small class="badge pull-right bg-red">3</small>
                            </a>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-bar-chart-o"></i>
                                <span>Charts</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
							
                            <ul class="treeview-menu">
                                <li><a href="pages/charts/morris.html"><i class="fa fa-angle-double-right"></i> Correlation Map</a></li>
                                <li><a href="pages/charts/flot.html"><i class="fa fa-angle-double-right"></i> Relation Ranking </a></li>
                                <li><a href="pages/charts/inline.html"><i class="fa fa-angle-double-right"></i> Relation Map </a></li>
                            </ul>
                        </li>
						<!--
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-laptop"></i>
                                <span>UI Elements</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="pages/UI/general.html"><i class="fa fa-angle-double-right"></i> General</a></li>
                                <li><a href="pages/UI/icons.html"><i class="fa fa-angle-double-right"></i> Icons</a></li>
                                <li><a href="pages/UI/buttons.html"><i class="fa fa-angle-double-right"></i> Buttons</a></li>
                                <li><a href="pages/UI/sliders.html"><i class="fa fa-angle-double-right"></i> Sliders</a></li>
                                <li><a href="pages/UI/timeline.html"><i class="fa fa-angle-double-right"></i> Timeline</a></li>
                            </ul>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-edit"></i> <span>Forms</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="pages/forms/general.html"><i class="fa fa-angle-double-right"></i> General Elements</a></li>
                                <li><a href="pages/forms/advanced.html"><i class="fa fa-angle-double-right"></i> Advanced Elements</a></li>
                                <li><a href="pages/forms/editors.html"><i class="fa fa-angle-double-right"></i> Editors</a></li>
                            </ul>
                        </li>
						-->
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-table"></i> <span>Tables</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="pages/tables/simple.html"><i class="fa fa-angle-double-right"></i> News By Country</a></li>
									<!--
                                <li><a href="pages/tables/data.html"><i class="fa fa-angle-double-right"></i> Data tables</a></li>
									-->
                            </ul>
                        </li>
 						<!--
                        <li>
                            <a href="pages/mailbox.html">
                                <i class="fa fa-envelope"></i> <span>Mailbox</span>
                                <small class="badge pull-right bg-yellow">12</small>
                            </a>
                        </li>
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-folder"></i> <span>Examples</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="pages/examples/invoice.html"><i class="fa fa-angle-double-right"></i> Invoice</a></li>
                                <li><a href="pages/examples/login.html"><i class="fa fa-angle-double-right"></i> Login</a></li>
                                <li><a href="pages/examples/register.html"><i class="fa fa-angle-double-right"></i> Register</a></li>
                                <li><a href="pages/examples/lockscreen.html"><i class="fa fa-angle-double-right"></i> Lockscreen</a></li>
                                <li><a href="pages/examples/404.html"><i class="fa fa-angle-double-right"></i> 404 Error</a></li>
                                <li><a href="pages/examples/500.html"><i class="fa fa-angle-double-right"></i> 500 Error</a></li>
                                <li><a href="pages/examples/blank.html"><i class="fa fa-angle-double-right"></i> Blank Page</a></li>
                            </ul>
                        </li>
						-->
                    </ul>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Dashboard
                        <small>Control panel</small>
                    </h1>
					<!--
                    <ol class="breadcrumb">
                        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
                        <li class="active">Dashboard</li>
                    </ol>
					-->
                </section>

                <!-- Main content -->
                <section class="content">

                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h3>
                                        10
                                    </h3>
                                    <p>
                                        #News Today
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-bag"></i>
                                </div>
                                <a href="#" class="small-box-footer">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h3>
                                        30<sup style="font-size: 20px"></sup>
                                    </h3>
                                    <p>
                                        #News This Week
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                                <a href="#" class="small-box-footer">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-yellow">
                                <div class="inner">
                                    <h3>
                                        70
                                    </h3>
                                    <p>
                                        #News This Month
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-person-add"></i>
                                </div>
                                <a href="#" class="small-box-footer">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                        <div class="col-lg-3 col-xs-6">
                            <!-- small box -->
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h3>
                                        420
                                    </h3>
                                    <p>
                                        #News This Quarter
                                    </p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-pie-graph"></i>
                                </div>
                                <a href="#" class="small-box-footer">
                                    More info <i class="fa fa-arrow-circle-right"></i>
                                </a>
                            </div>
                        </div><!-- ./col -->
                    </div><!-- /.row -->

                    <!-- top row -->
                    <div class="row">
                        <div class="col-xs-12 connectedSortable">
                            
                        </div><!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- Main row -->
                    <div class="row">
                        <!-- Left col -->
                        <section class="col-lg-6 connectedSortable"> 
						 <!-- Calendar -->
                            <div class="box box-warning">
                                <div class="box-header">
                                    <i class="fa fa-calendar"></i>
                                    <div class="box-title">Calendar</div>
   
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <!-- button with a dropdown -->
                                        <div class="btn-group">
                                            <button class="btn btn-warning btn-sm dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                                            <ul class="dropdown-menu pull-right" role="menu">
                                                <li><a href="#">Add new event</a></li>
                                                <li><a href="#">Clear events</a></li>
                                                <li class="divider"></li>
                                                <li><a href="#">View calendar</a></li>
                                            </ul>
                                        </div>
                                    </div> <!-- /. tools -->                                    
                                </div><!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <!--The calendar -->
                                    <div id="calendar"></div>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
							
                            <!-- Box (with bar chart) -->
                            <div class="box box-danger" id="loading-example">
                                <div class="box-header">
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">
                                        <button class="btn btn-danger btn-sm refresh-btn" data-toggle="tooltip" title="Reload"><i class="fa fa-refresh"></i></button>
                                        <button class="btn btn-danger btn-sm" data-widget='collapse' data-toggle="tooltip" title="Collapse"><i class="fa fa-minus"></i></button>
                                        <button class="btn btn-danger btn-sm" data-widget='remove' data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
                                    </div><!-- /. tools -->
                                    <i class="fa fa-cloud"></i>

                                    <h3 class="box-title">Relation Ranking</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body no-padding">
                                    <div class="row">
                                        <div class="col-sm-12" id="chart_div" style="height:400px;">
                                            <!-- bar chart -->
      
                                        </div>
                                    </div><!-- /.row - inside box -->
                                </div><!-- /.box-body -->
                                <div class="box-footer">
                                    <div class="row">
                                        <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <input type="text" class="knob" data-readonly="true" value="38" data-width="60" data-height="60" data-fgColor="#f56954"/>
                                            <div class="knob-label">GOOG</div>
                                        </div><!-- ./col -->
                                        <div class="col-xs-4 text-center" style="border-right: 1px solid #f4f4f4">
                                            <input type="text" class="knob" data-readonly="true" value="27" data-width="60" data-height="60" data-fgColor="#00a65a"/>
                                            <div class="knob-label">MSFT</div>
                                        </div><!-- ./col -->
                                        <div class="col-xs-4 text-center">
                                            <input type="text" class="knob" data-readonly="true" value="18" data-width="60" data-height="60" data-fgColor="#3c8dbc"/>
                                            <div class="knob-label">FB</div>
                                        </div><!-- ./col -->
                                    </div><!-- /.row -->
                                </div><!-- /.box-footer -->
                            </div><!-- /.box -->        
                            
                            <!-- Custom tabs (Charts with tabs)-->
                            <div class="nav-tabs-custom">
                                <!-- Tabs within a box -->
                                <ul class="nav nav-tabs pull-right">
									<!--
									<li><a href="#sales-chart" data-toggle="tab">Area</a></li>
                                    <li class="active"><a href="#chart_div5" data-toggle="tab">Donut</a></li>-->
                                    <li class="pull-left header"><i class="fa fa-inbox"></i>News Distribution</li>
									
                                </ul>
                                <div class="tab-content no-padding">
                                    <!-- Morris chart - Sales -->
									<div id="chart_div6" style="height: 100%;width: 100%;" align="center"></div>
									<div class="chart tab-pane active" id="chart_div5" style="position: relative; height: 300px;"></div>
                                    <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;"></div>

                                </div>
                            </div><!-- /.nav-tabs-custom -->

                            <!-- quick email widget -->
							<!--
                            <div class="box box-info">
                                <div class="box-header">
                                    <i class="fa fa-envelope"></i>
                                    <h3 class="box-title">Quick Email</h3>
                                    <!-- tools box -->
							<!--
                                    <div class="pull-right box-tools">
                                        <button class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="Remove"><i class="fa fa-times"></i></button>
                                    </div><!-- /. tools -->
							<!--
                                </div>
                                <div class="box-body">
                                    <form action="#" method="post">
                                        <div class="form-group">
                                            <input type="email" class="form-control" name="emailto" placeholder="Email to:"/>
                                        </div>
                                        <div class="form-group">
                                            <input type="text" class="form-control" name="subject" placeholder="Subject"/>
                                        </div>
                                        <div>
                                            <textarea class="textarea" placeholder="Message" style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                                        </div>
                                    </form>
                                </div>
                                <div class="box-footer clearfix">
                                    <button class="pull-right btn btn-default" id="sendEmail">Send <i class="fa fa-arrow-circle-right"></i></button>
                                </div>
                            </div>-->

                        </section><!-- /.Left col -->
                        <!-- right col (We are only adding the ID to make the widgets sortable)-->
                        <section class="col-lg-6 connectedSortable">
                            <!-- Map box -->
                            <div class="box box-primary">
                                <div class="box-header">
                                    <!-- tools box -->
                                    <div class="pull-right box-tools">                                        
                                        <button class="btn btn-primary btn-sm daterange pull-right" data-toggle="tooltip" title="Date range"><i class="fa fa-calendar"></i></button>
                                        <button class="btn btn-primary btn-sm pull-right" data-widget='collapse' data-toggle="tooltip" title="Collapse" style="margin-right: 5px;"><i class="fa fa-minus"></i></button>
                                    </div><!-- /. tools -->

                                    <i class="fa fa-map-marker"></i>
                                    <h3 class="box-title">
                                        Correlation Map
                                    </h3>
                                </div>
                                <div class="box-body no-padding">
                                    <div id="chart_div3" style="height: 300px;"></div>
                                    <div class="table-responsive">
                                        <!-- .table - Uses sparkline charts-->
									 <h3 class="box-title">
                                        News By Country 
                                    </h3>
                                        <table class="table table-striped">
                                            <tr>
                                                <th>Country</th>
                                                <th>Visitors</th>
                                                <th>Online</th>
                                                <th>Page Views</th>
                                            </tr>
                                            <tr>
                                                <td><a href="#">USA</a></td>
                                                <td><div id="sparkline-1"></div></td>
                                                <td>209</td>
                                                <td>239</td>
                                            </tr>
                                            <tr>
                                                <td><a href="#">India</a></td>
                                                <td><div id="sparkline-2"></div></td>
                                                <td>131</td>
                                                <td>958</td>
                                            </tr>
                                            <tr>
                                                <td><a href="#">Britain</a></td>
                                                <td><div id="sparkline-3"></div></td>
                                                <td>19</td>
                                                <td>417</td>
                                            </tr>
                                            <tr>
                                                <td><a href="#">Brazil</a></td>
                                                <td><div id="sparkline-4"></div></td>
                                                <td>109</td>
                                                <td>476</td>
                                            </tr>
                                            <tr>
                                                <td><a href="#">China</a></td>
                                                <td><div id="sparkline-5"></div></td>
                                                <td>192</td>
                                                <td>437</td>
                                            </tr>
                                            <tr>
                                                <td><a href="#">Australia</a></td>
                                                <td><div id="sparkline-6"></div></td>
                                                <td>1709</td>
                                                <td>947</td>
                                            </tr>
                                        </table><!-- /.table -->
                                    </div>
                                </div><!-- /.box-body-->
                                <div class="box-footer">
                                    <button class="btn btn-info"><i class="fa fa-download"></i> Generate PDF</button>
                                    <button class="btn btn-warning"><i class="fa fa-bug"></i> Report Bug</button>
                                </div>
                            </div>
                            <!-- /.box -->

                            <!-- Chat box -->
							
                            <div class="box box-success">
                                <div class="box-header">
                                    <h3 class="box-title"><i class="fa fa-comments-o"></i> Chat</h3>
                                    <div class="box-tools pull-right" data-toggle="tooltip" title="Status">
                                        <div class="btn-group" data-toggle="btn-toggle" >
                                            <button type="button" class="btn btn-default btn-sm active"><i class="fa fa-square text-green"></i></button>                                            
                                            <button type="button" class="btn btn-default btn-sm"><i class="fa fa-square text-red"></i></button>
                                        </div>
                                    </div>
                                </div>
								<div class="box-body chat" id="chart_div2"></div>
                                    <!-- chat item -->
							<!--
                                    <div class="item">
                                        <img src="img/avatar.png" alt="user image" class="online"/>
                                        <p class="message">
                                            <a href="#" class="name">
                                                <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 2:15</small>
                                                Mike Doe
                                            </a>
                                            I would like to meet you to discuss the latest news about
                                            the arrival of the new theme. They say it is going to be one the
                                            best themes on the market
                                        </p>
                                        <div class="attachment">
                                            <h4>Attachments:</h4>
                                            <p class="filename">
                                                Theme-thumbnail-image.jpg
                                            </p>
                                            <div class="pull-right">
                                                <button class="btn btn-primary btn-sm btn-flat">Open</button>
                                            </div>
                                        </div><!-- /.attachment -->
							<!--
                                    </div><!-- /.item -->
                                    <!-- chat item -->
							<!--
                                    <div class="item">
                                        <img src="img/avatar2.png" alt="user image" class="offline"/>
                                        <p class="message">
                                            <a href="#" class="name">
                                                <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 5:15</small>
                                                Jane Doe
                                            </a>
                                            I would like to meet you to discuss the latest news about
                                            the arrival of the new theme. They say it is going to be one the
                                            best themes on the market
                                        </p>
                                    </div><!-- /.item -->
                                    <!-- chat item -->
							<!--
                                    <div class="item">
                                        <img src="img/Apple-Logo.png" alt="user image" class="offline"/>
                                        <p class="message">
                                            <a href="#" class="name">
                                                <small class="text-muted pull-right"><i class="fa fa-clock-o"></i> 5:30</small>
                                                Susan Doe
                                            </a>
                                            I would like to meet you to discuss the latest news about
                                            the arrival of the new theme. They say it is going to be one the
                                            best themes on the market
                                        </p>
                                    </div><!-- /.item -->
							<!--
                                </div><!-- /.chat -->
							<!--
                                <div class="box-footer">
                                    <div class="input-group">
                                        <input class="form-control" placeholder="Type message..."/>
                                        <div class="input-group-btn">
                                            <button class="btn btn-success"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- /.box (chat box) -->

                            <!-- TO DO List WE DON'T NEED - ELR
                            <div class="box box-primary">
                                <div class="box-header">
                                    <i class="ion ion-clipboard"></i>
                                    <h3 class="box-title">To Do List</h3>
                                    <div class="box-tools pull-right">
                                        <ul class="pagination pagination-sm inline">
                                            <li><a href="#">&laquo;</a></li>
                                            <li><a href="#">1</a></li>
                                            <li><a href="#">2</a></li>
                                            <li><a href="#">3</a></li>
                                            <li><a href="#">&raquo;</a></li>
                                        </ul>
                                    </div>
                                </div><!-- /.box-header -->
							<!--
								<div class="box-body">
                                    <ul class="todo-list">
                                        <li>
                                            <!-- drag handle -->
							<!--
                                            <span class="handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>  
                                            <!-- checkbox -->
							<!--
                                            <input type="checkbox" value="" name=""/>                                            
                                            <!-- todo text -->
							<!--
                                            <span class="text">Design a nice theme</span>
                                            <!-- Emphasis label -->
							<!--
                                            <small class="label label-danger"><i class="fa fa-clock-o"></i> 2 mins</small>
                                            <!-- General tools such as edit or delete-->
                          <!--                  <div class="tools">
                                                <i class="fa fa-edit"></i>
                                                <i class="fa fa-trash-o"></i>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>                                            
                                            <input type="checkbox" value="" name=""/>
                                            <span class="text">Make the theme responsive</span>
                                            <small class="label label-info"><i class="fa fa-clock-o"></i> 4 hours</small>
                                            <div class="tools">
                                                <i class="fa fa-edit"></i>
                                                <i class="fa fa-trash-o"></i>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <input type="checkbox" value="" name=""/>
                                            <span class="text">Let theme shine like a star</span>
                                            <small class="label label-warning"><i class="fa fa-clock-o"></i> 1 day</small>
                                            <div class="tools">
                                                <i class="fa fa-edit"></i>
                                                <i class="fa fa-trash-o"></i>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <input type="checkbox" value="" name=""/>
                                            <span class="text">Let theme shine like a star</span>
                                            <small class="label label-success"><i class="fa fa-clock-o"></i> 3 days</small>
                                            <div class="tools">
                                                <i class="fa fa-edit"></i>
                                                <i class="fa fa-trash-o"></i>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <input type="checkbox" value="" name=""/>
                                            <span class="text">Check your messages and notifications</span>
                                            <small class="label label-primary"><i class="fa fa-clock-o"></i> 1 week</small>
                                            <div class="tools">
                                                <i class="fa fa-edit"></i>
                                                <i class="fa fa-trash-o"></i>
                                            </div>
                                        </li>
                                        <li>
                                            <span class="handle">
                                                <i class="fa fa-ellipsis-v"></i>
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <input type="checkbox" value="" name=""/>
                                            <span class="text">Let theme shine like a star</span>
                                            <small class="label label-default"><i class="fa fa-clock-o"></i> 1 month</small>
                                            <div class="tools">
                                                <i class="fa fa-edit"></i>
                                                <i class="fa fa-trash-o"></i>
                                            </div>
                                        </li>
                                    </ul>
                                </div><!-- /.box-body -->
							<!--
                                <div class="box-footer clearfix no-border">
                                    <button class="btn btn-default pull-right"><i class="fa fa-plus"></i> Add item</button>
                                </div>
                            </div><!-- /.box -->

                        </section><!-- right col -->
                    </div><!-- /.row (main row) -->

                </section><!-- /.content -->
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->

        <!-- add new calendar event modal -->


        <!-- jQuery 2.0.2 -->
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <!-- jQuery UI 1.10.3 -->
        <script src="js/jquery-ui-1.10.3.min.js" type="text/javascript"></script>
        <!-- Bootstrap -->
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <!-- Morris.js charts -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
        <script src="js/plugins/morris/morris.min.js" type="text/javascript"></script>
        <!-- Sparkline -->
        <script src="js/plugins/sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
        <!-- jvectormap -->
        <script src="js/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
        <script src="js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
        <!-- fullCalendar -->
        <script src="js/plugins/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
        <!-- jQuery Knob Chart -->
        <script src="js/plugins/jqueryKnob/jquery.knob.js" type="text/javascript"></script>
        <!-- daterangepicker -->
        <script src="js/plugins/daterangepicker/daterangepicker.js" type="text/javascript"></script>
        <!-- Bootstrap WYSIHTML5 -->
        <script src="js/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js" type="text/javascript"></script>
        <!-- iCheck -->
        <script src="js/plugins/iCheck/icheck.min.js" type="text/javascript"></script>

        <!-- AdminLTE App -->
        <script src="js/AdminLTE/app.js" type="text/javascript"></script>
        
        <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
        <script src="js/AdminLTE/dashboard.js" type="text/javascript"></script>
	
	
		<script type="text/javascript">
		<?php
				echo "var links = [\n";

				for($i=1;$i<=9;$i++){

					$query = "SELECT * FROM  `testcount` WHERE MAJOR_TICKER = '".$RELATED_TICKERS[$i]['symbol']."';";
					//echo $query;
					$result = mysql_query($query,$link);
					$row = mysql_fetch_array($result);

					$RELATED_TICKERS_2 = json_decode($row['RELATED_COMPANIES_JSON'],true);
					echo '{source: "'.$RELATED_TICKERS[0]['symbol'].'", target: "'.$RELATED_TICKERS[$i]['symbol'].'", type: "licensing"}'.",\n";

					for($j=1;$j<=9 && $j<sizeof($RELATED_TICKERS_2);$j++)
					{

						 echo '{source: "'.$RELATED_TICKERS[$i]['symbol'].'", target: "'.$RELATED_TICKERS_2[$j]['symbol'].'", type: "licensing"}';

						 if($i != 9 || $j != 9)
							echo ",\n";		
					}
				}
				echo '];';

				?>
				// http://blog.thomsonreuters.com/index.php/mobile-patent-suits-graphic-of-the-day/

				var nodes = {};

				// Compute the distinct nodes from the links.
				links.forEach(function(link) {
				  link.source = nodes[link.source] || (nodes[link.source] = {name: link.source});
				  link.target = nodes[link.target] || (nodes[link.target] = {name: link.target});
				});

				var width = 800,
					height = 300;
				if(window.innerWidth<800)
					width=300;
				var force = d3.layout.force()
					.nodes(d3.values(nodes))
					.links(links)
					.size([width, height])
					.linkDistance(40)
					.charge(-300)
					.on("tick", tick)
					.start();
				var wid = document.getElementById("chart_div3").clientWidth;
				var svg = d3.select("#chart_div3").append("svg")
					.attr("width", wid)
					.attr("height", window.innerHeight);

				var link = svg.selectAll(".link")
					.data(force.links())
				  .enter().append("line")
					.attr("class", "link");

				var node = svg.selectAll(".node")
					.data(force.nodes())
				  .enter().append("g")
					.attr("class", "node")
					.attr("id",function(d) { return d.name; })
					.on("mouseover", mouseover)
					.on("mouseout", mouseout)
					.call(force.drag);

				node.append("circle")
					.attr("r", 8);

				node.append("text")
					.attr("x", -20)
					.attr("y", -10)
					.text(function(d) { return d.name; });



				var Major_node = svg.selectAll(".node#"+<?php echo '"'.str_replace(':', '\\\:', $TICKER).'"'; ?>+" circle");
				Major_node.attr('r',22).style('fill','#39DF3B');

				<?php
					for($i=1;$i<=9;$i++){

					echo 'var Major_node = svg.selectAll(".node#'.str_replace(':', '\\\:', $RELATED_TICKERS[$i]['symbol']).' circle");';
					echo "Major_node.attr('r',10).style('fill','#ff33cc');\n";
					//echo $query;
					}
				?>
				function tick() {
				  link
					  .attr("x1", function(d) { return d.source.x; })
					  .attr("y1", function(d) { return d.source.y; })
					  .attr("x2", function(d) { return d.target.x; })
					  .attr("y2", function(d) { return d.target.y; });

				  node
					  .attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
				}

				function mouseover() {
				  d3.select(this).select("circle").transition()
					  .duration(0)
					  .attr("r", 16);
				}

				function mouseout() {

				  d3.select(this).select("circle").transition()
					  .duration(0)
					  .attr("r", 8);

					var Major_node = svg.selectAll(".node#"+<?php echo '"'.str_replace(':', '\\\:', $TICKER).'"'; ?>+" circle")
					Major_node.attr('r',22);

					<?php
					for($i=1;$i<=9;$i++){

					echo 'var Major_node = svg.selectAll(".node#'.str_replace(':', '\\\:', $RELATED_TICKERS[$i]['symbol']).' circle");';
					echo "Major_node.attr('r',10).style('fill','#ff33cc');\n";
					//echo $query;
					}
					?>
				}
		</script>
    </body>
</html>