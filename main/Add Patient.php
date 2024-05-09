<?php
session_start();

// Check if the 'admin' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true || !isset($_SESSION['adminID'])) {
    // Redirect the user to the login page
    header("Location: Admin Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once 'pawfect_connect.php';

// Get the AdminID from the sessionw
$adminID = $_SESSION['adminID'];

// Prepare and execute the SQL query to fetch admin information
$sql = "SELECT * FROM admininformation WHERE AdminID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $adminID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if there is a row returned
if ($row = mysqli_fetch_assoc($result)) {
    // Admin information retrieved successfully
    $firstName = $row['firstname'];
 
    $lastName = $row['lastname'];
    $adminPhoto = $row['adminphoto'];

    // Now you can use these variables to display the admin information in your HTML
} else {
    // Admin information not found
    echo "Admin information not found!";
}
$provincesAndCities = array(
  "Abra" => array("Bangued", "Boliney", "Bucay", "Bucloc", "Daguioman", "Danglas", "Dolores", "La Paz", "Lacub", "Lagangilang", "Lagayan", "Langiden", "Licuan-Baay", "Luba", "Malibcong", "Manabo", "Penarrubia", "Pidigan", "Pilar", "Sallapadan", "San Isidro", "San Juan", "San Quintin", "Tayum", "Tineg", "Tubo", "Villaviciosa"),
  "Agusan del Norte" => array("Buenavista", "Butuan City", "Cabadbaran City", "Carmen", "Jabonga", "Kitcharao", "Las Nieves", "Magallanes", "Nasipit", "Remedios T. Romualdez", "Santiago", "Tubay"),
  "Agusan del Sur" => array("Bayugan City", "Bunawan", "Esperanza", "La Paz", "Loreto", "Prosperidad (Capital)", "Rosario", "San Francisco", "San Luis", "Santa Josefa", "Sibagat", "Talacogon", "Trento", "Veruela"),
  "Aklan" => array("Altavas", "Balete", "Banga", "Batan", "Buruanga", "Ibajay", "Kalibo (Capital)", "Lezo", "Libacao", "Madalag", "Makato", "Malay", "Malinao", "Nabas", "New Washington", "Numancia", "Tangalan"),
  "Albay" => array("Bacacay", "Camalig", "Daraga (Locsin)", "Guinobatan", "Jovellar", "Legazpi City (Capital)", "Libon", "Ligao City", "Malilipot", "Malinao", "Manito", "Oas", "Pio Duran", "Polangui", "Rapu-Rapu", "Santo Domingo", "Tiwi"),
  "Antique" => array("Anini-y", "Barbaza", "Belison", "Bugasong", "Caluya", "Culasi", "Hamtic", "Laua-an", "Libertad", "Pandan", "Patnongon", "San Jose de Buenavista (Capital)", "San Remigio", "Sebaste", "Sibalom", "Tibiao", "Tobias Fornier", "Valderrama"),
  "Apayao" => array("Calanasan (Bayag)", "Conner", "Flora", "Kabugao (Capital)", "Luna", "Pudtol", "Santa Marcela"),
  "Aurora" => array("Baler (Capital)", "Casiguran", "Dilasag", "Dinalungan", "Dingalan", "Dipaculao", "Maria Aurora", "San Luis"),
  "Basilan" => array("Akbar", "Al-Barka", "Hadji Mohammad Ajul", "Hadji Muhtamad", "Isabela City (Capital)", "Lamitan City", "Lantawan", "Maluso", "Sumisip", "Tabuan-Lasa", "Tipo-Tipo", "Ungkaya Pukan"),
  "Bataan" => array("Abucay", "Bagac", "Balanga City (Capital)", "Dinalupihan", "Hermosa", "Limay", "Mariveles", "Morong", "Orani", "Orion", "Pilar", "Samal"),
  "Batanes" => array("Basco (Capital)", "Itbayat", "Ivana", "Mahatao", "Sabtang", "Uyugan"),
  "Batangas" => array("Agoncillo", "Alitagtag", "Balayan", "Balete", "Bauan", "Calaca", "Calatagan", "Cuenca", "Ibaan", "Laurel", "Lemery", "Lian", "Lipa City", "Lobo", "Mabini", "Malvar", "Mataasnakahoy", "Nasugbu", "Padre Garcia", "Rosario", "San Jose", "San Juan", "San Luis", "San Nicolas", "San Pascual", "Santa Teresita", "Santo Tomas", "Taal", "Talisay", "Tanauan City", "Taysan", "Tingloy", "Tuy"),
  "Benguet" => array("Atok", "Baguio City", "Bakun", "Bokod", "Buguias", "Itogon", "Kabayan", "Kapangan", "Kibungan", "La Trinidad (Capital)", "Mankayan", "Sablan", "Tuba", "Tublay"),
  "Biliran" => array("Almeria", "Biliran", "Cabucgayan", "Caibiran", "Culaba", "Kawayan", "Maripipi", "Naval (Capital)"),
  "Bohol" => array("Alburquerque", "Alicia", "Anda", "Antequera", "Baclayon", "Balilihan", "Batuan", "Bien Unido", "Bilar", "Buenavista", "Calape", "Candijay", "Carmen", "Catigbian", "Clarin", "Corella", "Cortes", "Dagohoy", "Danao", "Dauis", "Dimiao", "Duero", "Garcia Hernandez", "Getafe", "Guindulman", "Inabanga", "Jagna", "Lila", "Loay", "Loboc", "Loon", "Mabini", "Maribojoc", "Panglao", "Pilar", "President Carlos P. Garcia (Pitogo)", "Sagbayan (Borja)", "San Isidro", "San Miguel", "Sevilla", "Sierra Bullones", "Sikatuna", "Tagbilaran City (Capital)", "Talibon", "Trinidad", "Tubigon", "Ubay", "Valencia"),
  "Bukidnon" => array("Baungon", "Cabanglasan", "Damulog", "Dangcagan", "Don Carlos", "Impasugong", "Kadingilan", "Kalilangan", "Kibawe", "Kitaotao", "Lantapan", "Libona", "Malaybalay City (Capital)", "Malitbog", "Manolo Fortich", "Maramag", "Pangantucan", "Quezon", "San Fernando", "Sumilao", "Talakag"),
  "Bulacan" => array("Angat", "Balagtas (Bigaa)", "Baliuag", "Bocaue", "Bulacan", "Bustos", "Calumpit", "Doña Remedios Trinidad", "Guiguinto", "Hagonoy", "Malolos City (Capital)", "Marilao", "Meycauayan City", "Norzagaray", "Obando", "Pandi", "Paombong", "Plaridel", "Pulilan", "San Ildefonso", "San Jose del Monte City", "San Miguel", "San Rafael", "Santa Maria"),
  "Cagayan" => array("Abulug", "Alcala", "Allacapan", "Amulung", "Aparri", "Baggao", "Ballesteros", "Buguey", "Calayan", "Camalaniugan", "Claveria", "Enrile", "Gattaran", "Gonzaga", "Iguig", "Lal-lo", "Lasam", "Pamplona", "Peñablanca", "Piat", "Rizal", "Sanchez-Mira", "Santa Ana", "Santa Praxedes", "Santa Teresita", "Santo Niño (Faire)", "Solana", "Tuao", "Tuguegarao City (Capital)"),
  "Camarines Norte" => array("Basud", "Capalonga", "Daet (Capital)", "Jose Panganiban", "Labo", "Mercedes", "Paracale", "San Lorenzo Ruiz (Imelda)", "San Vicente", "Santa Elena", "Talisay", "Vinzons"),
  "Camarines Sur" => array("Baao", "Balatan", "Bato", "Bombon", "Buhi", "Bula", "Cabusao", "Calabanga", "Camaligan", "Canaman", "Caramoan", "Del Gallego", "Gainza", "Garchitorena", "Goa", "Iriga City", "Lagonoy", "Libmanan", "Lupi", "Magarao", "Milaor", "Minalabac", "Nabua", "Naga City (Capital)", "Ocampo", "Pamplona", "Pasacao", "Pili (Capital)", "Presentacion (Parubcan)", "Ragay", "Sagnay", "San Fernando", "San Jose", "Sipocot", "Siruma", "Tigaon", "Tinambac"),
  "Camiguin" => array("Catarman", "Guinsiliban", "Mahinog", "Mambajao (Capital)"),
  "Capiz" => array("Cuartero", "Dao", "Dumalag", "Dumarao", "Ivisan", "Jamindan", "Maayon", "Mambusao", "Panay", "Panitan", "Pilar", "Pontevedra", "President Roxas", "Roxas City (Capital)", "Sapi-an", "Sigma", "Tapaz"),
  "Catanduanes" => array("Bagamanoc", "Baras", "Bato", "Caramoran", "Gigmoto", "Pandan", "Panganiban (Payo)", "San Andres (Calolbon)", "San Miguel", "Viga", "Virac (Capital)"),
  "Cavite" => array("Alfonso", "Amadeo", "Bacoor City", "Carmona", "Cavite City", "Dasmariñas City", "General Emilio Aguinaldo", "General Mariano Alvarez", "General Trias", "Imus City (Capital)", "Indang", "Kawit", "Magallanes", "Maragondon", "Mendez", "Naic", "Noveleta", "Rosario", "Silang", "Tagaytay City", "Tanza", "Ternate", "Trece Martires City (Capital)"),
  "Cebu" => array("Alcantara", "Alcoy", "Alegria", "Aloguinsan", "Argao", "Asturias", "Badian", "Balamban", "Bantayan", "Barili", "Bogo City", "Boljoon", "Borbon", "Carcar City", "Carmen", "Catmon", "Cebu City (Capital)", "Compostela", "Consolacion", "Cordoba", "Daanbantayan", "Dalaguete", "Danao City", "Dumanjug", "Ginatilan", "Liloan", "Madridejos", "Malabuyoc", "Mandaue City", "Medellin", "Minglanilla", "Moalboal", "Naga City", "Oslob", "Pilar", "Pinamungajan", "Poro", "Ronda", "Samboan", "San Fernando", "San Francisco", "San Remigio", "Santa Fe", "Santander", "Sibonga", "Sogod", "Tabogon", "Tabuelan", "Talisay City", "Toledo City", "Tuburan", "Tudela"),
  "Cotabato" => array("Alamada", "Aleosan", "Antipas", "Arakan", "Banisilan", "Carmen", "Kabacan", "Kidapawan City (Capital)", "Libungan", "M'lang", "Magpet", "Makilala", "Matalam", "Midsayap", "Pigcawayan", "Pikit", "President Roxas", "Roxas", "Tulunan"),
  "Davao de Oro" => array("Compostela", "Laak (San Vicente)", "Mabini (Dona Alicia)", "Maco", "Maragusan (San Mariano)", "Mawab", "Monkayo", "Montevista", "Nabunturan (Capital)", "New Bataan", "Pantukan"),
  "Davao del Norte" => array("Asuncion (Saug)", "Braulio E. Dujali", "Carmen", "Kapalong", "New Corella", "Panabo City", "Samal City", "San Isidro", "Santo Tomas", "Tagum City (Capital)", "Talaingod"),
  "Davao del Sur" => array("Bansalan", "Davao City", "Digos City (Capital)", "Hagonoy", "Kiblawan", "Magsaysay", "Malalag", "Matanao", "Padada", "Santa Cruz", "Sulop"),
  "Davao Occidental" => array("Don Marcelino", "Jose Abad Santos (Trinidad)", "Malita (Capital)", "Santa Maria", "Sarangani"),
  "Davao Oriental" => array("Baganga", "Banaybanay", "Boston", "Caraga", "Cateel", "Governor Generoso", "Lupon", "Manay", "Mati City (Capital)", "San Isidro", "Tarragona"),
  "Dinagat Islands" => array("Basilisa (Rizal)", "Cagdianao", "Dinagat", "Loreto", "San Jose (Capital)"),
  "Eastern Samar" => array("Arteche", "Balangiga", "Balangkayan", "Borongan City (Capital)", "Can-avid", "Dolores", "General MacArthur", "Giporlos", "Guiuan", "Hernani", "Jipapad", "Lawaan", "Llorente", "Maslog", "Maydolong", "Mercedes", "Oras", "Quinapondan", "Salcedo", "San Julian", "San Policarpo", "Sulat", "Taft"),
  "Guimaras" => array("Buenavista", "Jordan (Capital)", "Nueva Valencia", "San Lorenzo", "Sibunag"),
  "Ifugao" => array("Aguinaldo", "Alfonso Lista (Potia)", "Asipulo", "Banaue", "Hingyon", "Hungduan", "Kiangan", "Lagawe (Capital)", "Lamut", "Mayoyao", "Tinoc"),
  "Ilocos Norte" => array("Adams", "Bacarra", "Badoc", "Bangui", "Banna (Espiritu)", "Batac City", "Burgos", "Carasi", "Currimao", "Dingras", "Dumalneg", "Laoag City (Capital)", "Marcos", "Nueva Era", "Pagudpud", "Paoay", "Pasuquin", "Piddig", "Pinili", "San Nicolas", "Sarrat", "Solsona", "Vintar"),
  "Ilocos Sur" => array("Alilem", "Banayoyo", "Bantay", "Burgos", "Cabugao", "Candon City", "Caoayan", "Cervantes", "Galimuyod", "Gregorio del Pilar (Concepcion)", "Lidlidda", "Magsingal", "Nagbukel", "Narvacan", "Quirino (Angkaki)", "Salcedo (Baugen)", "San Emilio", "San Esteban", "San Ildefonso", "San Juan (Lapog)", "San Vicente", "Santa", "Santa Catalina", "Santa Cruz", "Santa Lucia", "Santa Maria", "Santiago", "Santo Domingo", "Sigay", "Sinait", "Sugpon", "Suyo", "Tagudin", "Vigan City (Capital)"),
  "Iloilo" => array("Ajuy", "Alimodian", "Anilao", "Badiangan", "Balasan", "Banate", "Barotac Nuevo", "Barotac Viejo", "Batad", "Bingawan", "Cabatuan", "Calinog", "Carles", "Concepcion", "Dingle", "Dueñas", "Dumangas", "Estancia", "Guimbal", "Igbaras", "Iloilo City (Capital)", "Janiuay", "Lambunao", "Leganes", "Lemery", "Leon", "Maasin", "Miagao", "Mina", "New Lucena", "Oton", "Passi City", "Pavia", "Pototan", "San Dionisio", "San Enrique", "San Joaquin", "San Miguel", "San Rafael", "Santa Barbara", "Sara", "Tigbauan", "Tubungan", "Zarraga"),
  "Isabela" => array("Alicia", "Angadanan", "Aurora", "Benito Soliven", "Burgos", "Cabagan", "Cabatuan", "Cordon", "Delfin Albano (Magsaysay)", "Dinapigue", "Divilacan", "Echague", "Gamu", "Ilagan City (Capital)", "Jones", "Luna", "Maconacon", "Mallig", "Naguilian", "Palanan", "Quezon", "Quirino", "Ramon", "Reina Mercedes", "Roxas", "San Agustin", "San Guillermo", "San Isidro", "San Manuel", "San Mariano", "San Mateo", "San Pablo", "Santa Maria", "Santiago City", "Santo Tomas", "Tumauini"),
  "Kalinga" => array("Balbalan", "Lubuagan", "Pasil", "Pinukpuk", "Rizal (Liwan)", "Tabuk City (Capital)", "Tanudan", "Tinglayan"),
  "La Union" => array("Agoo", "Aringay", "Bacnotan", "Bagulin", "Balaoan", "Bangar", "Bauang", "Burgos", "Caba", "Luna", "Naguilian", "Pugo", "Rosario", "San Fernando City (Capital)", "San Gabriel", "San Juan", "Santo Tomas", "Santol", "Sudipen", "Tubao"),
  "Laguna" => array("Alaminos", "Bay", "Biñan City", "Cabuyao City", "Calamba City (Capital)", "Calauan", "Cavinti", "Famy", "Kalayaan", "Liliw", "Los Baños", "Luisiana", "Lumban", "Mabitac", "Magdalena", "Majayjay", "Nagcarlan", "Paete", "Pagsanjan", "Pakil", "Pangil", "Pila", "Rizal", "San Pablo City", "San Pedro City", "Santa Cruz (Capital)", "Santa Maria", "Santa Rosa City", "Siniloan", "Victoria"),
  "Lanao del Norte" => array("Bacolod", "Baloi", "Baroy", "Iligan City", "Kapatagan", "Kauswagan", "Kolambugan", "Lala", "Linamon", "Magsaysay", "Maigo", "Matungao", "Munai", "Nunungan", "Pantao Ragat", "Pantar", "Poona Bayabao (Gata)", "Salvador", "Sapad", "Sultan Naga Dimaporo (Karomatan)", "Tagoloan", "Tangcal", "Tubod (Capital)"),
  "Lanao del Sur" => array("Bacolod-Kalawi (Bacolod Grande)", "Balabagan", "Balindong (Watu)", "Bayang", "Binidayan", "Buadiposo-Buntong", "Bubong", "Butig", "Calanogas", "Ditsaan-Ramain", "Ganassi", "Kapai", "Kapatagan", "Lumba-Bayabao (Maguing)", "Lumbaca-Unayan", "Lumbatan", "Lumbayanague", "Madalum", "Madamba (Pagayawan)", "Maguing", "Malabang", "Marantao", "Marogong", "Masiu", "Mulondo", "Pagayawan (Tatarikan)", "Piagapo", "Picong (Sultan Gumander)", "Poona Bayabao (Gata)", "Pualas", "Saguiaran", "Sultan Dumalondong", "Picong", "Tagoloan II", "Tamparan", "Taraka", "Tubaran", "Tugaya", "Wao"),
  "Leyte" => array("Abuyog", "Alangalang", "Albuera", "Babatngon", "Barugo", "Bato", "Baybay City", "Burauen", "Calubian", "Capoocan", "Carigara", "Dagami", "Dulag", "Hilongos", "Hindang", "Inopacan", "Isabel", "Jaro", "Javier (Bugho)", "Julita", "Kananga", "La Paz", "Leyte", "MacArthur", "Mahaplag", "Matag-ob", "Matalom", "Mayorga", "Merida", "Ormoc City", "Palo", "Palompon", "Pastrana", "San Isidro", "San Miguel", "Santa Fe", "Tabango", "Tabontabon", "Tacloban City (Capital)", "Tanauan", "Tolosa", "Tunga", "Villaba"),
  "Maguindanao" => array("Ampatuan", "Barira", "Buldon", "Buluan", "Datu Abdullah Sangki", "Datu Anggal Midtimbang", "Datu Blah T. Sinsuat", "Datu Hoffer Ampatuan", "Datu Montawal (Pagagawan)", "Datu Odin Sinsuat (Dinaig)", "Datu Paglas", "Datu Piang (Dulawan)", "Datu Salibo", "Datu Saudi-Ampatuan", "Datu Unsay", "General Salipada K. Pendatun", "Guindulungan", "Kabuntalan (Tumbao)", "Mamasapano", "Mangudadatu", "Matanog", "Northern Kabuntalan", "Pagalungan", "Paglat", "Pandag", "Parang", "Rajah Buayan", "Shariff Aguak (Maganoy)", "South Upi", "Sultan Kudarat", "Sultan Mastura", "Sultan Sa Barongis (Lambayong)", "Sultan Sumagka (Talitay)", "Talayan", "Talitay", "Upi"),
  "Marinduque" => array("Boac (Capital)", "Buenavista", "Gasan", "Mogpog", "Santa Cruz", "Torrijos"),
  "Masbate" => array("Aroroy", "Baleno", "Balud", "Batuan", "Cataingan", "Cawayan", "Claveria", "Dimasalang", "Esperanza", "Mandaon", "Masbate City (Capital)", "Milagros", "Mobo", "Monreal", "Palanas", "Pio V. Corpuz (Limbuhan)", "Placer", "San Fernando", "San Jacinto", "San Pascual", "Uson"),
  "Misamis Occidental" => array("Aloran", "Baliangao", "Bonifacio", "Calamba", "Clarin", "Concepcion", "Don Victoriano Chiongbian (Don Mariano Marcos)", "Jimenez", "Lopez Jaena", "Oroquieta City (Capital)", "Ozamiz City", "Panaon", "Plaridel", "Sapang Dalaga", "Sinacaban", "Tangub City", "Tudela"),
  "Misamis Oriental" => array("Alubijid", "Balingasag", "Balingoan", "Binuangan", "Cagayan de Oro City (Capital)", "Claveria", "El Salvador City", "Gingoog City", "Gitagum", "Initao", "Jasaan", "Kinoguitan", "Lagonglong", "Laguindingan", "Libertad", "Lugait", "Magsaysay (Linugos)", "Manticao", "Medina", "Naawan", "Opol", "Salay", "Sugbongcogon", "Tagoloan", "Talisayan", "Villanueva"),
  "Mountain Province" => array("Barlig", "Bauko", "Besao", "Bontoc (Capital)", "Natonin", "Paracelis", "Sabangan", "Sadanga", "Sagada", "Tadian"),
  "Negros Occidental" => array("Bacolod City", "Bago City", "Binalbagan", "Cadiz City", "Calatrava", "Candoni", "Cauayan", "Enrique B. Magalona (Saravia)", "Escalante City", "Himamaylan City", "Hinigaran", "Hinoba-an (Asia)", "Ilog", "Isabela", "Kabankalan City", "La Carlota City", "La Castellana", "Manapla", "Moises Padilla (Magallon)", "Murcia", "Pontevedra", "Pulupandan", "Sagay City", "Salvador Benedicto", "San Carlos City", "San Enrique", "Silay City", "Sipalay City", "Talisay City", "Toboso", "Valladolid", "Victorias City"),
  "Negros Oriental" => array("Amlan (Ayuquitan)", "Ayungon", "Bacong", "Bais City", "Basay", "Bayawan City (Tulong)", "Bindoy (Payabon)", "Canlaon City", "Dauin", "Dumaguete City (Capital)", "Guihulngan", "Jimalalud", "La Libertad", "Mabinay", "Manjuyod", "Pamplona", "San Jose", "Santa Catalina", "Siaton", "Sibulan", "Tanjay City", "Tayasan", "Valencia (Luzurriaga)", "Vallehermoso", "Zamboanguita"),
  "Northern Samar" => array("Allen", "Biri", "Bobon", "Capul", "Catubig", "Catarman (Capital)", "Gamay", "Laoang", "Lapinig", "Las Navas", "Lavezares", "Lope de Vega", "Mapanas", "Mondragon", "Palapag", "Pambujan", "Rosario", "San Antonio", "San Isidro", "San Jose", "San Roque", "San Vicente", "Silvino Lobos", "Victoria"),
  "Nueva Ecija" => array("Aliaga", "Bongabon", "Cabanatuan City", "Cabiao", "Carranglan", "Cuyapo", "Gabaldon (Bitulok & Sabani)", "Gapan City", "General Mamerto Natividad", "General Tinio (Papaya)", "Guimba", "Jaen", "Laur", "Licab", "Llanera", "Lupao", "Muñoz City", "Nampicuan", "Palayan City (Capital)", "Pantabangan", "Peñaranda", "Quezon", "Rizal", "San Antonio", "San Isidro", "San Jose City", "San Leonardo", "Santa Rosa", "Santo Domingo", "Science City of Muñoz", "Talavera", "Talugtug", "Zaragoza"),
  "Nueva Vizcaya" => array("Alfonso Castañeda", "Ambaguio", "Aritao", "Bagabag", "Bambang (Capital)", "Bayombong", "Diadi", "Dupax del Norte", "Dupax del Sur", "Kasibu", "Kayapa", "Quezon", "Santa Fe", "Solano", "Villaverde (Ibung)"),
  "Occidental Mindoro" => array("Abra de Ilog", "Calintaan", "Looc", "Lubang", "Magsaysay", "Mamburao (Capital)", "Paluan", "Rizal", "Sablayan", "San Jose", "Santa Cruz"),
  "Oriental Mindoro" => array("Baco", "Bansud", "Bongabong", "Bulalacao (San Pedro)", "Calapan City (Capital)", "Gloria", "Mansalay", "Naujan", "Pinamalayan", "Pola", "Puerto Galera", "Roxas", "San Teodoro", "Socorro", "Victoria"),
  "Palawan" => array("Aborlan", "Agutaya", "Araceli", "Balabac", "Bataraza", "Brooke's Point", "Busuanga", "Cagayancillo", "Coron", "Culion", "Cuyo", "Dumaran", "El Nido (Bacuit)", "Kalayaan", "Linapacan", "Magsaysay", "Narra", "Puerto Princesa City (Capital)", "Quezon", "Rizal (Marcos)", "Roxas", "San Vicente", "Sofronio Española", "Taytay"),
  "Pampanga" => array("Angeles City", "Apalit", "Arayat", "Bacolor", "Candaba", "Floridablanca", "Guagua", "Lubao", "Mabalacat City", "Macabebe", "Magalang", "Masantol", "Mexico", "Minalin", "Porac", "San Fernando City (Capital)", "San Luis", "San Simon", "Santa Ana", "Santa Rita", "Santo Tomas", "Sasmuan"),
  "Pangasinan" => array("Agno", "Aguilar", "Alaminos City", "Alcala", "Anda", "Asingan", "Balungao", "Bani", "Basista", "Bautista", "Bayambang", "Binalonan", "Binmaley", "Bolinao", "Bugallon", "Burgos", "Calasiao", "Dasol", "Infanta", "Labrador", "Laoac", "Lingayen (Capital)", "Mabini", "Malasiqui", "Manaoag", "Mangaldan", "Mangatarem", "Mapandan", "Natividad", "Pozorrubio", "Rosales", "San Carlos City", "San Fabian", "San Jacinto", "San Manuel", "San Nicolas", "San Quintin", "Santa Barbara", "Santa Maria", "Santo Tomas", "Sison", "Sual", "Tayug", "Umingan", "Urbiztondo", "Villasis"),
  "Quezon" => array("Agdangan", "Alabat", "Atimonan", "Buenavista", "Burdeos", "Calauag", "Candelaria", "Catanauan", "Dolores", "General Luna", "General Nakar", "Guinayangan", "Gumaca", "Infanta", "Jomalig", "Lopez", "Lucban", "Lucena City (Capital)", "Macalelon", "Mauban", "Mulanay", "Padre Burgos", "Pagbilao", "Panukulan", "Patnanungan", "Perez", "Pitogo", "Plaridel", "Polillo", "Quezon", "Real", "Sampaloc", "San Andres", "San Antonio", "San Francisco (Aurora)", "San Narciso", "Sariaya", "Tagkawayan", "Tayabas City", "Tiaong", "Unisan"),
  "Quirino" => array("Aglipay", "Cabarroguis (Capital)", "Diffun", "Maddela", "Nagtipunan", "Saguday"),
  "Rizal" => array("Angono", "Antipolo City", "Baras", "Binangonan", "Cainta", "Cardona", "Jalajala", "Morong", "Pililla", "Rodriguez (Montalban)", "San Mateo", "Tanay", "Taytay", "Teresa"),
  "Romblon" => array("Alcantara", "Banton (Jones)", "Cajidiocan", "Calatrava", "Concepcion", "Corcuera", "Ferrol", "Looc", "Magdiwang", "Odiongan (Capital)", "Romblon", "San Agustin", "San Andres", "San Fernando", "San Jose", "Santa Fe", "Santa Maria"),
  "Samar (Western Samar)" => array("Almagro", "Basey", "Calbayog City", "Calbiga", "Catbalogan City (Capital)", "Daram", "Gandara", "Hinabangan", "Jiabong", "Marabut", "Matuguinao", "Motiong", "Pagsanghan", "Paranas (Wright)", "Pinabacdao", "San Jorge", "San Jose de Buan", "San Sebastian", "Santa Margarita", "Santa Rita", "Santo Niño", "Tagapul-an", "Talalora", "Tarangnan", "Villareal", "Zumarraga"),
  "Sarangani" => array("Alabel (Capital)", "Glan", "Kiamba", "Maasim", "Maitum", "Malapatan", "Malungon"),
  "Siquijor" => array("Enrique Villanueva", "Larena", "Lazi", "Maria", "San Juan", "Siquijor (Capital)"),
  "Sorsogon" => array("Barcelona", "Bulan", "Bulusan", "Casiguran", "Castilla", "Donsol", "Gubat", "Irosin", "Juban", "Magallanes", "Matnog", "Pilar", "Prieto Diaz", "Santa Magdalena", "Sorsogon City (Capital)"),
  "South Cotabato" => array("Banga", "General Santos City (Dadiangas) (Capital)", "Koronadal City", "Lake Sebu", "Norala", "Polomolok", "Santo Niño", "Surallah", "T'boli", "Tampakan", "Tantangan", "Tupi"),
  "Southern Leyte" => array("Anahawan", "Bontoc", "Hinunangan", "Hinundayan", "Libagon", "Liloan", "Limasawa", "Maasin City (Capital)", "Macrohon", "Malitbog", "Padre Burgos", "Pintuyan", "Saint Bernard", "San Francisco", "San Juan (Cabalian)", "San Ricardo", "Silago", "Sogod", "Tomas Oppus"),
  "Sultan Kudarat" => array("Bagumbayan", "Columbio", "Esperanza", "Isulan (Capital)", "Kalamansig", "Lambayong (Mariano Marcos)", "Lebak", "Lutayan", "Palimbang", "President Quirino", "Senator Ninoy Aquino", "Tacurong City"),
  "Sulu" => array("Banguingui (Tongkil)", "Hadji Panglima Tahil (Marunggas)", "Indanan", "Jolo (Capital)", "Kalingalan Caluang", "Lugus", "Luuk", "Maimbung", "Old Panamao", "Omar", "Pandami", "Panglima Estino (New Panamao)", "Pangutaran", "Parang", "Pata", "Patikul", "Siasi", "Talipao", "Tapul"),
  "Surigao del Norte" => array("Alegria", "Bacuag", "Burgos", "Claver", "Dapa", "Del Carmen", "General Luna", "Gigaquit", "Mainit", "Malimono", "Pilar", "Placer", "San Benito", "San Francisco (Anao-aon)", "San Isidro", "Santa Monica (Sapao)", "Sison", "Socorro", "Surigao City (Capital)", "Tagana-an", "Tubod"),
  "Surigao del Sur" => array("Barobo", "Bayabas", "Bislig City", "Cagwait", "Cantilan", "Carmen", "Carrascal", "Cortes", "Hinatuan", "Lanuza", "Lianga", "Lingig", "Madrid", "Marihatag", "San Agustin", "San Miguel", "Tagbina", "Tago", "Tandag City (Capital)"),
  "Tarlac" => array("Anao", "Bamban", "Camiling", "Capas", "Concepcion", "Gerona", "La Paz", "Mayantoc", "Moncada", "Paniqui", "Pura", "Ramos", "San Clemente", "San Jose", "San Manuel", "Santa Ignacia", "Tarlac City (Capital)", "Victoria"),
  "Tawi-Tawi" => array("Bongao (Capital)", "Languyan", "Mapun (Cagayan de Tawi-Tawi)", "Panglima Sugala (Balimbing)", "Sapa-Sapa", "Sibutu", "Simunul", "Sitangkai", "South Ubian", "Tandubas", "Turtle Islands (Taganak)"),
  "Zambales" => array("Botolan", "Cabangan", "Candelaria", "Castillejos", "Iba (Capital)", "Masinloc", "Olongapo City", "Palauig", "San Antonio", "San Felipe", "San Marcelino", "San Narciso", "Santa Cruz", "Subic"),
  "Zamboanga del Norte" => array("Baliguian", "Dapitan City", "Dipolog City (Capital)", "Godod", "Gutalac", "Jose Dalman (Ponot)", "Kalawit", "Katipunan", "La Libertad", "Labason", "Leon B. Postigo (Bacungan)", "Liloy", "Manukan", "Mutia", "Piñan (New Piñan)", "Polanco", "Pres. Manuel A. Roxas", "Rizal", "Salug", "Sergio Osmeña Sr.", "Siayan", "Sibuco", "Sibutad", "Sindangan", "Siocon", "Sirawai", "Tampilisan"),
  "Zamboanga del Sur" => array("Aurora", "Bayog", "Dimataling", "Dinas", "Dumalinao", "Dumingag", "Guipos", "Josefina", "Kumalarang", "Labangan", "Lakewood", "Lapuyan", "Mahayag", "Margosatubig", "Midsalip", "Molave", "Pagadian City (Capital)", "Pitogo", "Ramon Magsaysay (Liargo)", "San Miguel", "San Pablo", "Sominot", "Tabina", "Tambulig", "Tigbao", "Tukuran", "Vincenzo A. Sagun"),
  "Zamboanga Sibugay" => array("Alicia", "Buug", "Diplahan", "Imelda", "Ipil (Capital)", "Kabasalan", "Mabuhay", "Malangas", "Naga", "Olutanga", "Payao", "Roseller Lim", "Siay", "Talusan", "Titay", "Tungawan")
);


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="Favicon 2.png" type="image/png">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet"> <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="hamburgers.css" rel="stylesheet">
  <link href="userdashboard.css" rel="stylesheet">
  <title>Add Patient</title>
<style>
  .red{
    color:red;
  }
  .error-message{
    color:red;
    font-size: 11px;
  }
  .error-border {
  border: 1px solid red !important;
}
  </style>

</head>
<body>
<div class="container-fluid">
    <div class="main-container">
        <!-- Header and Sidebar -->
        <?php include 'admin_header.php'; ?>
        <div class="sidebar">
            <?php include 'sidebar.php'; ?>
        </div>


<!--Profile Picture and Details-->
        <div class="content" id="content">
    <div class="row justify-content-center mb-5 mt-4">

        <div class="col-md-11"> 
            <div class="card">
                <div class="card-header card-header-patient-form text-center">
                    <h3> <b>ADD PATIENT</b> <h3>
                </div>
                <div class="card-body px-5">
                <div class="form-container">
  <div class="step-indicator" id="step-indicator">
    <div class="step-item">
      <span class="number active">1</span>
    </div>
    <div class="line-container">
      <hr class="lines active">
    </div>
    <div class="step-item">
      <span class="number">2</span>
    </div>
    <div class="line-container">
      <hr class="lines">
    </div>
    <div class="step-item">
      <span class="number">3</span>
    </div>
  </div>
</div>

                <div class="form-container">
  <div class="step-indicator" id="step-indicator">
    <div class="step-item mr-5">
      <div class="text-indicator active">Patient Details</div>
    </div>
  
    <div class="step-item ml-2 mr-5">
      <div class="text-indicator">Bite Exposure Details</div>
    </div>

    <div class="step-item ml-1">
      <div class="text-indicator">Treatment Given</div>
    </div>
  </div>
</div>



<form id="multi-step-form" enctype="multipart/form-data">
  <div class="step active" id="step1">
    <div class="row justify-content-center mt-3 mb-0 px-3">
      <div class="col-lg-4 form-group px-4 mb-0">
        <label for="fName">First Name<span class="red">*</span></label>
        <input type="text" id="fName" name="fName" placeholder="First Name" class="form-control" required><br><br>
      </div>
      <div class="col-lg-4 form-group px-4 mb-0">
        <label for="mName">Middle Name</label>
        <input type="text" id="mName" name="mName" placeholder="Middle Name" class="form-control" ><br><br>
      </div>
      <div class="col-lg-4 form-group px-4 mb-0">
        <label for="lName">Last Name<span class="red">*</span></label>
        <input type="text" id="lName" name="lName" placeholder="Last Name"class="form-control"  required><br><br>
      </div>
    </div>
    <div class="row justify-content-center px-3">
      <div class="col-lg-4 form-group px-4 mb-0">
        <label for="birthDate">Birth Date<span class="red">*</span></label>
        <input type="date" id="birthDate" name="birthDate" placeholder="Birth Date" class="form-control" required max="<?php echo date('Y-m-d', strtotime('-1 year')); ?>"onkeydown="return false"><br><br>

      </div>
  
      <div class="col-lg-4 form-group px-4 mb-0">
        <label for="age">Age<span class="red">*</span></label>
        <input type="number" id="age" name="age" placeholder="Age" class="form-control"required ><br><br>
      </div>
   
      <div class="col-lg-4 form-group px-4 mb-0">
        <label for="sex">Sex<span class="red">*</span></label>
        <select id="sex" name="sex" class="form-control" required >
          <option value="">Select Sex</option>
          <option value="male">Male</option>
          <option value="female">Female</option>
          <option value="other">Other</option>
        </select><br><br>
      </div>
    </div>
    <div class="row justify-content-center px-3 mb-0">
      <div class="col-lg-4 form-group px-4">
        <label for="weight">Weight (kg)<span class="red">*</span></label>
        <input type="number" id="weight" name="weight" placeholder="Weight (kg)" class="form-control"  required><br><br>
      </div>
      <div class="col-lg-4 form-group px-4">
        <label for="phoneNumber">Phone Number<span class="red">*</span></label>
        <input type="tel" id="phoneNumber" name="phoneNumber" placeholder="Phone Number" class="form-control" required><br><br>
      </div>
      <div class="col-lg-4 form-group px-4">
        <label for="email">Email Address<span class="red">*</span></label>
        <input type="email" id="email" name="email" placeholder="Email Address" class="form-control" required><br><br>
      </div>
    </div>
    <div class="row justify-content-center px-3">
      <div class="col-lg-4 form-group px-4">
      <label for="province">Province<span class="red">*</span></label>
      <select id="provinceSelect" name="province" class="form-control" required >
    <option value="">Select Province</option>
    <?php foreach ($provincesAndCities as $province => $cities): ?>
        <option value="<?php echo $province; ?>"><?php echo $province; ?></option>
    <?php endforeach; ?>
</select>
      </div>
      <div class="col-lg-4 form-group px-4">
      <label for="city">City<span class="red">*</span></label>
      <select id="citySelect" name="city" class="form-control" required >
    <option value="">Select City</option>
</select>

      </div>
      <div class="col-lg-4 form-group px-4">
      <label for="address">Address<span class="red">*</span></label>
        <input type="text" id="address" name="address" class="form-control" placeholder="Address" required ><br><br>
      </div>
    </div>
    <div class="row justify-content-center px-3">
      <div class="col-lg-4 form-group px-4">
        <label for="emergencyContact">In case of Emergency, notify<span class="red">*</span></label>
        <input type="text" id="emergencyContact" name="emergencyContact" placeholder="Full Name" class="form-control" required ><br><br>
      </div>
      <div class="col-lg-4 form-group px-4">
        <label for="relationship">Relationship<span class="red">*</span></label>
        <select id="emergencyContactRelationship" name="emergency_contact_relationship" class="form-control" required >
    <option value="">Select Relationship</option>
    <option value="Spouse">Spouse</option>
    <option value="Parent">Parent</option>
    <option value="Child">Child</option>
    <option value="Sibling">Sibling</option>
    <option value="Other Family Member">Other Family Member </option>
    <option value="Friend">Friend</option>
    <option value="Colleague">Colleague</option>
    <option value="Neighbor">Neighbor</option>
    <option value="Guardian">Guardian</option>
    <option value="Legal Representative">Legal Representative</option>
    <option value="Partner">Partner</option>
    <option value="Caretaker">Caretaker</option>
    <option value="Doctor">Doctor</option>
    <option value="Other">Other</option>
</select>

      </div>
      <div class="col-lg-4 form-group px-4">
        <label for="emergencyPhoneNumber">Emergency Phone Number<span class="red">*</span></label>
        <input type="tel" id="emergencyPhoneNumber" name="emergencyPhoneNumber" placeholder="Emergency Phone Number" class="form-control" required ><br><br>
      </div>
    </div>
    <div class="row justify-content-center">
    <button class="btn-customized" onclick="nextStep(1)">Next</button>
</div>
  </div>



  <div class="step" id="step2">
    <div class="row justify-content-center mt-3">
        <div class="col-lg-5 form-group mx-auto p-0">
            <label for="exposureDate">Date of Exposure</label>
            <input type="date" id="exposureDate" name="exposureDate" class="form-control" placeholder="Date of Exposure" max="<?php echo date('Y-m-d'); ?>" required>
        </div>
        <div class="col-lg-5 form-group  mx-auto p-0">
            <label for="exposureBy">Exposure by</label>
            <select id="exposureBy" name="exposureBy" class="form-control" required>
    <option value="">Select Option</option>
    <option value="Bite">Bite</option>
    <option value="Scratch">Scratch</option>
    <option value="Saliva Contact with Open Wound">Saliva Contact with Open Wound</option>
    <option value="Saliva Contact with Mucous Membrane">Saliva Contact with Mucous Membrane</option>
</select>
        </div>
        </div>
 
    <div class="row justify-content-center mt-3">
    <div class="col-lg-3 form-group mx-auto mb-0 p-0">
    <label for="exposureType">Type of Exposure</label>
<select id="exposureType" name="exposureType" class="form-control" required>
    <option value="">Select Type of Exposure</option>
    <option value="Category I">Category I</option>
    <option value="Category II">Category II</option>
    <option value="Category III">Category III</option>
    <option value="Category IV">Category IV</option>
</select>

        </div>
        <div class="col-lg-3 form-group mx-auto mb-0 p-0">
            <label for="animalType">Type of Animal</label>
            <input type="text" id="animalType" name="animalType" placeholder="Type of Animal" class="form-control" required><br><br>
        </div>
        <div class="col-lg-3 form-group mx-auto mb-0 p-0">
            <label for="biteLocation">Bite Location</label>
            <input type="text" id="biteLocation" name="biteLocation" placeholder="Bite Location" class="form-control" required><br><br>
        </div>
        </div>
        <div class="row justify-content-center mt-0">
    <div class="col-lg-11 form-group patient-form mx-auto p-0">
            <label for="uploadImage">Upload Image</label>
            <input type="file" id="uploadImage" name="uploadImage" class="form-control" accept="image/jpeg, image/png"><br><br>
        </div>

        </div>
        <div class="row mt-0 mx-auto justify-content-center">
    <button class="prev mr-5 btn btn-outline-custom" onclick="prevStep(2)">Previous</button>
    <button onclick="nextStep(2)" class="btn-customized">Next</button>
    </div>
</div>
<div class="step" id="step3">
  <div class="step3-container">
  <div id="medicineItems">
    <!-- Initial medicine item -->
    <div class="row justify-content-center align-items-end mx-auto pt-4 pb-0 mb-3 medicine-item">
        <div class="col-lg-3 form-group mx-auto p-0 mb-0 pb-0">
            <label for="medicineType">Type of Medicine</label>
            <select name="medicineType[]" class="form-control medicineType" required>
                <option value="">Select Type of Medicine</option>
                <?php
                // Assuming you have a connection to your database
                // Fetch data from the "medicine" table
                $sql = "SELECT * FROM medicine";
                $result = mysqli_query($conn, $sql);
                
                // Loop through the results and generate options for the dropdown
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['MedicineID'] . "'>" . $row['MedicineName'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="col-lg-3 form-group mx-auto p-0 mb-0 pb-0">
            <label for="medicineGiven">Medicine Given</label>
            <select name="medicineGiven[]" class="form-control medicineGiven" required>
            </select>
        </div>

        <div class="col-lg-1 form-group mx-auto p-0 mb-0 pb-0">
            <label for="dosageQuantity">Dosage</label>
            <input type="text" name="dosageQuantity[]" class="form-control" placeholder="Dosage Quantity" required>
        </div>
        
        <div class="col-lg-1 form-group mx-auto p-0 mb-0 pb-0">
    <label for="route">Route</label>
    <input type="text" name="route[]" placeholder="Route" class="form-control route" readonly required>
</div>

        
        <div class="col-lg-1 form-group mx-auto p-0 mb-0 pb-0">
            <label for="quantity">Quantity</label>
            <input type="number" name="quantity[]" class="form-control" placeholder="Quantity" required>
        </div>
        
        <div class="col-lg-1 mx-auto form-group mb-0 pb-0 mt-auto">
            <div class="d-flex justify-content-center">
                <button class="btn btn-add btn-success align-self-end mr-3 addMedicineItem">+</button>
                <button class="btn btn-add btn-danger align-self-end removeMedicineItem">-</button>
            </div>
        </div>
    </div>
</div>

<div id="equipmentItems">
    <!-- Initial equipment item row -->
    <div class="row mx-auto justify-content-center align-items-end mt-3 equipment-item">
        <div class="col-lg-7 form-group mx-auto mb-0 pb-0 pl-0 mr-3">
            <label for="equipmentType">Type of Equipment</label>
            <select name="equipmentType[]" class="form-control equipmentType" required>
                <option value="">Select Type of Equipment</option>
                <?php
                // Assuming you have a connection to your database
                // Fetch data from the "equipment" table
                $sql = "SELECT * FROM equipment";
                $result = mysqli_query($conn, $sql);

                // Loop through the results and generate options for the dropdown
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<option value='" . $row['EquipmentID'] . "'>" . $row['Name'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-lg-3 form-group mx-auto p-0 mb-0 pb-0">
            <label for="equipmentAmount">Equipment Amount</label>
            <input type="text" name="equipmentAmount[]" class="form-control equipmentAmount" placeholder="Equipment Amount" required>
        </div>
        <div class="col-lg-1 mx-auto form-group mb-0 pb-0 mt-auto">
            <div class="d-flex justify-content-center">
                <button class="btn btn-add btn-success align-self-end mr-3 addEquipmentItem">+</button>
                <button class="btn btn-add btn-danger align-self-end removeEquipmentItem">-</button>
            </div>
        </div>
    </div>
</div>





    <div class="row justify-content-center mx-auto pt-4 pb-0 mb-0">
    <div class="col-lg-4 form-group mx-auto p-0 mb-0 pb-0">
    <label for="treatmentCategory">Treatment Category</label>
    <select id="treatmentCategory" name="treatmentCategory" class="form-control" required>
        <option value="">Select Treatment Category</option>
        <option value="pre-exposure">Pre Exposure</option>
        <option value="post-exposure">Post Exposure</option>
    </select>
</div>

        <div class="col-lg-4 form-group mx-auto p-0 mb-0 pb-0">
    <label for="sessions">Sessions</label>
    <select id="sessions" name="sessions" class="form-control" required>
    <option value="">Select Session</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
    </select>
</div>

        <div class="col-lg-3 form-group mx-auto p-0 mb-0 pb-0">
    <label for="treatmentDate">Date of Treatment</label>
    <input type="date" id="treatmentDate" name="treatmentDate" value="<?php echo date('Y-m-d'); ?>" placeholder="Date of Treatment" class="form-control" required readonly>
</div>

    </div>

    <div class="row align-items-end justify-content-center mx-auto pt-4 pb-0 mb-0 ml-1">
    <div class="col-lg-12 form-group mx-auto p-0 mb-0 pb-0">
        <label for="doctorRemarks">Doctor Remarks</label>
        <textarea id="doctorRemarks" name="doctorRemarks" placeholder="Doctor Remarks" class="form-control w-100"></textarea>
    </div>
</div>

    
    <div class="row justify-content-center mt-5">
        <button class="prev mr-5 btn btn-outline-custom" onclick="prevStep(3)">Previous</button>
        <button type="submit" class="btn-customized">Submit</button>
    </div>
</div>
</div>

  </form>
</div>
</div>
</div>  
</div>
</div>










                       
    
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    
    <!-- Data Table JS -->
<script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>


 
  
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



<script>



document.addEventListener("DOMContentLoaded", function() {
    var animalTypeInput = document.getElementById("animalType");
    var biteLocationInput = document.getElementById("biteLocation");

    // Function to validate input and allow only letters and spaces
    function validateInput(inputField) {
        inputField.addEventListener("keypress", function(event) {
            var keyCode = event.keyCode;
            // Allow letters and spaces (ASCII codes: 65-90, 97-122, 32)
            if ((keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || keyCode === 32) {
                return true;
            } else {
                event.preventDefault();
                return false;
            }
        });
    }

    // Apply input validation to animalType and biteLocation inputs
    validateInput(animalTypeInput);
    validateInput(biteLocationInput);
});

    document.getElementById("profileDropdown").addEventListener("mousedown", function(event) {
    event.preventDefault(); // Prevent the default action of the mousedown event
    var dropdownContent = document.getElementById("dropdownContent");
    if (dropdownContent.style.display === "block") {
        dropdownContent.style.display = "none";
    } else {
        dropdownContent.style.display = "block";
    }
});

 // Get all sidebar items
const sidebarItems = document.querySelectorAll('.sidebar-item');

// Loop through each sidebar item
sidebarItems.forEach(function(sidebarItem) {
    // Find the icon within the sidebar item
    const sidebarIcon = sidebarItem.querySelector('.sidebar-icon');

    // Get the paths to the default and hover icons from data attributes
    const defaultIcon = sidebarItem.dataset.defaultIcon;
    const hoverIcon = sidebarItem.dataset.hoverIcon;

    // Add mouseenter event listener
    sidebarItem.addEventListener('mouseenter', function() {
        // Change the source of the icon to the hover icon upon hover
        sidebarIcon.src = hoverIcon;
    });

    // Add mouseleave event listener
    sidebarItem.addEventListener('mouseleave', function() {
        // Change the source of the icon back to the default icon upon mouse leave
        sidebarIcon.src = defaultIcon;
    });
});

$(document).ready(function () {
    $('#sidebarCollapse1').on('click', function () {
        $('#sidebar').toggleClass('collapsed'); // Toggle 'collapsed' class on #sidebar
        $('#content').toggleClass('collapsed'); // Toggle 'collapsed' class on #content
    });
});



  const inputFields = document.querySelectorAll('input, select');
inputFields.forEach(function(field) {
  field.addEventListener('input', function() {
    if (field.checkValidity()) {
      removeError(field);
    }
  });
});
let currentStep = 1;
const stepIndicator = document.querySelectorAll('.step-indicator span');
const formSteps = document.querySelectorAll('.step');
const lines = document.querySelectorAll('.lines');
const textindicator = document.querySelectorAll('.text-indicator');
// Function to validate required fields in the current step
function validateStep(step) {
  const fields = formSteps[step -1].querySelectorAll('input[required], select[required], textarea[required]');
  for (let i = 0; i < fields.length; i++) {
    if (!fields[i].value) {
      alert('Please fill in all required fields before proceeding.');
      return false; // Validation failed
    }
  }
  return true; // Validation passed
}



// Modify nextStep function to include validation
function nextStep(step) {
  let isValid = true;

  // Check for empty required fields in the current step
  const currentStepFields = formSteps[currentStep - 1].querySelectorAll('input[required], select[required]');
  currentStepFields.forEach(function(field) {
    if (!field.value.trim()) {
      showError(field, 'This field is required.');
      isValid = false;
    } else {
      removeError(field);
    }
  });

  // If there are empty required fields, stop and show error messages
  if (!isValid) {
    return;
  }

  // Validate specific fields based on the current step
  if (currentStep === 1) {
    validateBirthDateField();
    validateAgeField();
    validatePhoneNumberField();
    validateEmergencyPhoneNumberField();
    validateWeightField();
    validateEmailField();
  }

  // If there are validation errors in the current step, stop and show error messages
  if (formSteps[currentStep - 1].querySelector('.error')) {
    return;
  }

  // Proceed to the next step if all fields are valid
  if (step < formSteps.length) {
    formSteps[currentStep - 1].classList.remove('active');
    stepIndicator[currentStep - 1].classList.remove('active');
    textindicator[currentStep - 1].classList.remove('active');

    currentStep++;
    formSteps[currentStep - 1].classList.add('active');
    stepIndicator[currentStep - 1].classList.add('active');
    textindicator[currentStep - 1].classList.add('active');

    if (currentStep <= lines.length) {
      lines[currentStep - 1].classList.add('active');
    }
  }
}



// Modify prevStep function to exclude validation
function prevStep(step) {
  if (step > 1) {
    // Proceed to the previous step
    formSteps[currentStep - 1].classList.remove('active');
    stepIndicator[currentStep - 1].classList.remove('active');
    textindicator[currentStep - 1].classList.remove('active');
    currentStep--;
    formSteps[currentStep - 1].classList.add('active');
    stepIndicator[currentStep - 1].classList.add('active');
    textindicator[currentStep - 1].classList.add('active');
    if (currentStep <= lines.length) {
      lines[currentStep - 1].classList.add('active');
    }
  }
}
function showErrorBorder(field) {
  field.classList.add('error-border');
}

// Function to remove error border
function removeErrorBorder(field) {
  field.classList.remove('error-border');
}
document.getElementById('multi-step-form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Check if all input fields have a value
   const inputFields = document.querySelectorAll('input[required], select[required], textarea[required]');

    let allFieldsFilled = true;
    inputFields.forEach(function(field) {
        if (!field.value.trim()) {
            showError(field, 'This field is required.');
            allFieldsFilled = false;
        } else {
            removeError(field);
        }
    });

    // If any field is empty, stop and show error messages
    if (!allFieldsFilled) {
        return;
    }

    // Perform AJAX submission or other actions
    // Create FormData object
    var formData = new FormData(this);

    // Perform AJAX submission or other actions
    $.ajax({
        url: 'submit.php', // Replace 'submit.php' with your actual form submission endpoint
        method: 'POST',
        data: formData,
        contentType: false, // Important for file uploads
        processData: false, // Important for file uploads
        success: function(response) {
            // Handle success response
            console.log(response);
            // Optionally, you can reset the form after successful submission
            window.location.href = 'patient-list.php'; // Replace 'PatientList.php' with the actual URL of your Patient List page
        },
        error: function(xhr, status, error) {
            // Handle error response
            console.error(error);
        }
    });
});




// Define JavaScript variable to hold PHP-generated data
var provincesAndCities = <?php echo json_encode($provincesAndCities); ?>;

// Get references to the province and city select elements
var provinceSelect = document.getElementById("provinceSelect");
var citySelect = document.getElementById("citySelect");

// Add event listener to the province select element
provinceSelect.addEventListener("change", function() {
    // Clear previous options in the city select element
    citySelect.innerHTML = "<option value=''>Select City</option>";
    
    // Get the selected province
    var selectedProvince = this.value;
    
    // Get the cities for the selected province from JavaScript variable
    var cities = provincesAndCities[selectedProvince];
    
    // Add options for cities to the city select element
    if (cities) {
        cities.forEach(function(city) {
            var option = document.createElement("option");
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
    }
});

  // Function to validate birth date
  function validateBirthDate(birthDate) {
    const currentDate = new Date();
    const selectedDate = new Date(birthDate);
    const oneYearAgo = new Date(currentDate.getFullYear() - 1, currentDate.getMonth(), currentDate.getDate());

    return selectedDate <= oneYearAgo;
  }

  // Function to validate age
  function validateAge(age) {
    return age >= 1 && age <= 123;
  }

  // Function to validate phone number format
  function validatePhoneNumber(phoneNumber) {
    const phoneNumberRegex = /^09\d{9}$/;
    return phoneNumberRegex.test(phoneNumber);
  }
  function validateEmergencyPhoneNumber(emergencyPhoneNumber) {
    const phoneNumberRegex = /^09\d{9}$/;
    return phoneNumberRegex.test(emergencyPhoneNumber);
  }

  // Function to validate weight
  function validateWeight(weight) {
    return weight >= 0 && weight <= 650;
  }

  // Function to validate email address format
  function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // Function to display error message and apply red border
// Function to display error message and apply red border
function showError(field, message) {
  const errorContainer = field.nextElementSibling;
  if (!errorContainer || !errorContainer.classList.contains('error-message')) {
    field.classList.add('error-border');
    const errorMessage = document.createElement('div');
    errorMessage.classList.add('error-message');
    errorMessage.textContent = message;
    field.parentNode.insertBefore(errorMessage, field.nextElementSibling);
  } else {
    errorContainer.textContent = message;
  }
  field.classList.add('error');
}

// Function to remove error message and red border
function removeError(field) {
  const errorContainer = field.nextElementSibling;
  if (errorContainer && errorContainer.classList.contains('error-message')) {
    field.parentNode.removeChild(errorContainer);
    field.classList.remove('error');
    field.classList.remove('error-border'); // Remove this line
  }
}


  // Function to validate birth date field
  function validateBirthDateField() {
    const birthDate = document.getElementById('birthDate').value;
    const isValid = validateBirthDate(birthDate);
    if (!isValid) {
      showError(document.getElementById('birthDate'), 'Birth date should be at least one year earlier than the current date.');
    } else {
      removeError(document.getElementById('birthDate'));
    }
  }

  // Function to validate age field
  function validateAgeField() {
    const age = parseInt(document.getElementById('age').value, 10);
    const isValid = validateAge(age);
    if (!isValid) {
      showError(document.getElementById('age'), 'Age should be between 1 and 123.');
    } else {
      removeError(document.getElementById('age'));
    }
  }

  // Function to validate phone number field
  function validatePhoneNumberField() {
    const phoneNumber = document.getElementById('phoneNumber').value;
    const isValid = validatePhoneNumber(phoneNumber);
    if (!isValid) {
      showError(document.getElementById('phoneNumber'), 'Phone number should start with "09" and have 11 digits.');
    } else {
      removeError(document.getElementById('phoneNumber'));
    }
  }
  function validateEmergencyPhoneNumberField() {
    const emergencyPhoneNumber = document.getElementById('emergencyPhoneNumber').value;
    const isValid = validateEmergencyPhoneNumber(emergencyPhoneNumber);
    if (!isValid) {
      showError(document.getElementById('emergencyPhoneNumber'), 'Phone number should start with "09" and have 11 digits.');
    } else {
      removeError(document.getElementById('emergencyPhoneNumber'));
    }
  }

  // Function to validate weight field
  function validateWeightField() {
    const weight = parseFloat(document.getElementById('weight').value);
    const isValid = validateWeight(weight);
    if (!isValid) {
      showError(document.getElementById('weight'), 'Weight should be between 0 and 650 kg.');
    } else {
      removeError(document.getElementById('weight'));
    }
  }

  // Function to validate email field
  function validateEmailField() {
    const email = document.getElementById('email').value;
    const isValid = validateEmail(email);
    if (!isValid) {
      showError(document.getElementById('email'), 'Please enter a valid email address.');
    } else {
      removeError(document.getElementById('email'));
    }
  }

  // Add event listeners to input fields to validate them as the user types
  document.getElementById('birthDate').addEventListener('input', validateBirthDateField);
  document.getElementById('age').addEventListener('input', validateAgeField);
  document.getElementById('phoneNumber').addEventListener('input', validatePhoneNumberField);
  document.getElementById('emergencyPhoneNumber').addEventListener('input', validateEmergencyPhoneNumberField);
  document.getElementById('weight').addEventListener('input', validateWeightField);
  document.getElementById('email').addEventListener('input', validateEmailField);

  $(document).ready(function() {
    // Function to fetch brands based on selected medicine type
    function fetchBrands($medicineTypeDropdown, $medicineGivenDropdown, $routeInput) {
    var medicineId = $medicineTypeDropdown.val();
    console.log("Sending AJAX request with medicineType:", medicineId);
    $.ajax({
        url: 'fetch-brands.php', // Change this to the path of your PHP script
        method: 'POST',
        data: { medicineType: medicineId },
        dataType: 'json',
        success: function(response) {
            $medicineGivenDropdown.empty(); // Clear previous options
            $medicineGivenDropdown.append('<option value="">Select Brand</option>'); // Add the default option
            $.each(response, function(index, value) {
                
                $medicineGivenDropdown.append('<option value="' + value.MedicineBrandID + '">' + value.BrandName + '</option>');
                // Autofill the readonly route input field with the route of the selected brand
                $('.route').val(value.Route);
            });
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            console.log(`Response Text: ${xhr.responseText}`);
            // Handle errors
        }
    });
}


    // Event listener for the change event on medicine type dropdown
    $(document).on('change', '.medicineType', function() {
        var $medicineGivenDropdown = $(this).closest('.medicine-item').find('.medicineGiven');
        fetchBrands($(this), $medicineGivenDropdown);
    });

    function addMedicineItem() {
    // Clone the template of the medicine item
    var $newMedicineItem = $('#medicineItems .medicine-item').first().clone();

    // Clear the values of input fields in the cloned item
    $newMedicineItem.find('input').val('');

    // Append the cloned item to the container
    $('#medicineItems').append($newMedicineItem);

    // Trigger the change event on the medicine type dropdown in the cloned item
    $newMedicineItem.find('.medicineType').trigger('change');
}



    // Event listener for the "Add Medicine" button
    $(document).on('click', '.addMedicineItem', function() {
        addMedicineItem();
    });

    // Event listener for the "Remove Medicine" button
    $(document).on('click', '.removeMedicineItem', function() {
    // Check if there's more than one medicine item
    if ($('.medicine-item').length > 1) {
        // Remove the clicked medicine item
        $(this).closest('.medicine-item').remove();
    } else {
        // Alert the user or perform any other action indicating that there must be at least one item
        alert("At least one medicine item is required.");
    }
});
});
$(document).ready(function() {
    // Function to add a new equipment row
 // Function to add a new equipment item
function addEquipmentItem() {
    // Clone the template of the equipment item
    var $newEquipmentItem = $('#equipmentItems .equipment-item').first().clone();

    // Clear the values of input fields in the cloned item
    $newEquipmentItem.find('input').val('');

    // Append the cloned item to the container
    $('#equipmentItems').append($newEquipmentItem);
}

// Event listener for the "Add Equipment" button
$(document).on('click', '.addEquipmentItem', function() {
    addEquipmentItem();
});

// Event listener for the "Remove Equipment" button
$(document).on('click', '.removeEquipmentItem', function() {
    // Check if there's more than one equipment item
    if ($('.equipment-item').length > 1) {
        // Remove the clicked equipment item
        $(this).closest('.equipment-item').remove();
    } else {
        // Alert the user or perform any other action indicating that there must be at least one item
        alert("At least one equipment item is required.");
    }
});

});


</script>

<!-- Existing JavaScript and closing body tag -->


</body>
</html>
