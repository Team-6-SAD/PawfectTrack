<?php
session_start();

// Check if the 'admin' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true || !isset($_SESSION['adminID'])) {
    // Redirect the user to the login page
    header("Location: Admin Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once 'backend/pawfect_connect.php';

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
  <link rel="icon" href="img/Favicon 2.png" type="image/png">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="css/hamburgers.css" rel="stylesheet">
  <link href="css/userdashboard.css" rel="stylesheet">
  <title>Add Patient</title>
<style>
  .red{
    color:red;
  }
  .error-message{
    color:red;
    font-size: 11px;
    font-weight: bold;
  }
  .error-border {
  border: 1px solid red !important;
}
.invalid-feedback {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 80%;
    color: #dc3545;
    display: none;
}

  </style>
 <script>
    function calculateAge() {
      const birthDate = document.getElementById('birthDate').value;
      const ageField = document.getElementById('age');
      if (birthDate) {
        const birthDateObj = new Date(birthDate);
        const today = new Date();
        let age = today.getFullYear() - birthDateObj.getFullYear();
        const monthDiff = today.getMonth() - birthDateObj.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDateObj.getDate())) {
          age--;
        }
        ageField.value = age;
      } else {
        ageField.value = '';
      }
    }
  </script>

</head>

<body>
<div class="container-fluid">
    <div class="main-container">
        <!-- Header and Sidebar -->
        <?php include 'includes/admin_header.php'; ?>
        <div class="sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </div>


<!--Profile Picture and Details-->
        <div class="content" id="content">
          
    <div class="row justify-content-center mb-5 mt-4">

        <div class="col-md-11"> 
          
            <div class="card" style="background-color: #f5f5f5;">
                <div class="card-header card-header-patient-form text-center">
                    <h3> <b>ADD PATIENT RECORD</b> <h3>
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
      <div class="text-indicator"> Exposure Details</div>
    </div>

    <div class="step-item ml-1">
      <div class="text-indicator">Treatment Given</div>
    </div>
  </div>
</div>


<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999; position:fixed;"></div>
<form id="multi-step-form" enctype="multipart/form-data">
  <div class="step active" id="step1">
    <div class="row justify-content-center mt-3 px-3 ">
    <div class="col-lg-3 form-group px-4 mb-3">
    <label for="fName">First Name<span class="red">*</span></label>
    <input type="text" id="fName" name="fName" placeholder="First Name" class="form-control" oninput="preventLeadingSpace(event)" required maxlength="50">
  </div>
  <div class="col-lg-3 form-group px-4 mb-3">
    <label for="mName">Middle Name</label>
    <input type="text" id="mName" name="mName" placeholder="Middle Name" class="form-control" oninput="preventLeadingSpace(event)" maxlength="50">
  </div>
  <div class="col-lg-3 form-group px-4 mb-3">
    <label for="lName">Last Name<span class="red">*</span></label>
    <input type="text" id="lName" name="lName" placeholder="Last Name" class="form-control" oninput="preventLeadingSpace(event)" required maxlength="50">
  </div>
  <div class="col-lg-3 form-group px-4 mb-3">
    <label for="suffix">Suffix</label>
    <select id="suffix" name="suffix" class="form-control" onchange="toggleCustomSuffixInput(this.value)">
      <option value="" disabled selected>Select Suffix</option>
      <option value="Jr">Jr.</option>
      <option value="Sr">Sr.</option>
      <option value="II">II</option>
      <option value="III">III</option>
      <option value="IV">IV</option>
      <option value="V">V</option>
      <option value="Other">Other</option>
    </select>
    <input type="text" id="customSuffix" name="customSuffix" placeholder="Enter suffix" class="form-control mt-2" style="display: none;" maxlength="8">
  </div>
</div>
    <div class="row justify-content-center px-3">
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="birthDate">Birth Date<span class="red">*</span></label>
       <input type="date" id="birthDate" name="birthDate" placeholder="Birth Date" class="form-control" required 
       min="<?php echo date('Y-m-d', strtotime('-124 years')); ?>"
       max="<?php echo date('Y-m-d', strtotime(' -1 year')); ?>"
       onkeydown="return false"
       onchange="calculateAge(); setMinExposureDate();">


      </div>
  
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="age">Age<span class="red">*</span></label>
        <input type="tel" id="age" name="age" placeholder="Age" class="form-control" readonly>
      </div>
   
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="sex">Sex<span class="red">*</span></label>
        <select id="sex" name="sex" class="form-control" required >
          <option value="" disabled selected>Select Sex</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
      </div>
    </div>
    <div class="row justify-content-center px-3 mb-0">
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="weight">Weight (kg)<span class="red">*</span></label>
        <input type="tel" id="weight" name="weight" placeholder="Weight (kg)" class="form-control" maxlength="6"  required>
      </div>
         <div class="col-lg-4 form-group px-4 mb-3">
                                <label for="phoneNumber">Phone Number<span class="red">*</span></label>
                                    <input type="tel" id="phoneNumber" name="phoneNumber" class="form-control" placeholder="09123456789" style="min-width: 140px" maxlength="11" required >
                                <small id="phone-number-error" class="error-message"></small>
                            
                            </div>
                            
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="Email Address" class="form-control" maxlength="320">
      </div>
    </div>
    <div class="row justify-content-center px-3 mb-0">
      <div class="col-lg-4 form-group px-4 mb-3">
      <label for="province">Province<span class="red">*</span></label>
      <select id="provinceSelect" name="province" class="form-control" required >
    <option value="" disabled selected>Select Province</option>
    <?php foreach ($provincesAndCities as $province => $cities): ?>
        <option value="<?php echo $province; ?>"><?php echo $province; ?></option>
    <?php endforeach; ?>
</select>
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
      <label for="city">City<span class="red">*</span></label>
      <select id="citySelect" name="city" class="form-control" required >
    <option value="" disabled selected>Select City</option>
</select>

      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
      <label for="address">Address<span class="red">*</span></label>
        <input type="text" id="address" name="address" class="form-control" placeholder="Address" oninput="preventLeadingSpace(event)" required maxlength="150">
      </div>
    </div>
    <div class="row justify-content-center px-3 mb-3">
      <div class="col-lg-4 form-group px-4">
        <label for="emergencyContact">In case of Emergency, notify<span class="red">*</span></label>
        <input type="text" id="emergencyContact" name="emergencyContact" placeholder="Full Name" class="form-control" required oninput="preventLeadingSpace(event)" maxlength="100" >
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="relationship">Relationship<span class="red">*</span></label>
        <select id="emergencyContactRelationship" name="emergency_contact_relationship" class="form-control" required >
    <option value="" disabled selected>Select Relationship</option>
    <option value="Spouse">Spouse</option>
    <option value="Parent">Parent</option>
    <option value="Child">Child</option>
    <option value="Sibling">Sibling</option>
    <option value="Friend">Friend</option>
    <option value="Colleague">Colleague</option>
    <option value="Neighbor">Neighbor</option>
    <option value="Guardian">Guardian</option>
    <option value="Legal Representative">Legal Representative</option>
    <option value="Partner">Partner</option>
    <option value="Caretaker">Caretaker</option>
</select>

      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
                                <label for="emergencyPhoneNumber">Phone Number<span class="red">*</span></label>
                          

                                    <input type="tel" id="emergencyPhoneNumber" name="emergencyPhoneNumber" class="form-control" placeholder="09123456789" style="min-width: 140px" required maxlength="11" >
                          
                                <small id="emergency-phone-number-error" class="error-message"></small>
                            
                            </div>
    <div class="row justify-content-center">
    <button type="button" class="btn-customized" onclick="nextStep(1)" style="border-radius: 27.5px !important;">Next</button>
</div>
  </div>
  </div>


  <div class="step" id="step2">
    <div class="row justify-content-center mt-3">
        <div class="col-lg-5 form-group mx-auto p-0">
            <label for="exposureDate">Date of Exposure</label>
            <input type="date" id="exposureDate" name="exposureDate" class="form-control" placeholder="Date of Exposure" onkeydown="return false" max="<?php echo date('Y-m-d'); ?>" >
        </div>
        <div class="col-lg-5 form-group  mx-auto p-0">
            <label for="exposureBy">Exposure by</label>
            <select id="exposureBy" name="exposureBy" class="form-control" >
    <option value="" disabled selected>Select Option</option>
    <option value="Bite">Bite</option>
    <option value="Scratch">Scratch</option>
    <option value="Saliva Contact with Open Wound">Saliva Contact with Open Wound</option>
    <option value="Saliva Contact with Mucous Membrane">Saliva Contact with Mucous Membrane</option>
</select>
        </div>
        </div>
 
    <div class="row justify-content-center mt-3 mb-4">
    <div class="col-lg-3 form-group mx-auto mb-0 p-0">
    <label for="exposureType">Type of Exposure</label>
<select id="exposureType" name="exposureType" class="form-control" >
    <option value="" disabled selected>Select Type of Exposure</option>
    <option value="Category I">Category I</option>
    <option value="Category II">Category II</option>
    <option value="Category III">Category III</option>
    <option value="Category IV">Category IV</option>
</select>

        </div>
        <div class="col-lg-3 form-group mx-auto mb-0 p-0">
    <label for="animalType">Type of Animal</label>
    <select id="animalType" name="animalType" class="form-control" onchange="checkOtherOption()">
        <option value="" disabled selected>Select Animal Type</option>
        <option value="Bat">Bat</option>
        <option value="Dog">Dog</option>
        <option value="Cat">Cat</option>
        <option value="Cow">Cow</option>
        <option value="Other">Other</option>
    </select>
    <input type="text" id="otherAnimalType" name="otherAnimalType" placeholder="Type of Animal" class="form-control mt-2 d-none" maxlength="50">
</div>
        <div class="col-lg-3 form-group mx-auto mb-0 p-0">
            <label for="biteLocation">Exposed Location</label>
            <input type="text" id="biteLocation" name="biteLocation" placeholder="Bite Location" class="form-control" oninput="preventLeadingSpace(event)"  maxlength="50">
        </div>
        </div>
        <div class="row justify-content-center mt-0">
    <div class="col-lg-11 form-group patient-form mx-auto p-0 mb-2">
            <label for="uploadImages">Upload Image of Exposure</label>
            <input type="file" id="uploadImages" name="uploadImages[]" class="form-control" accept="image/jpeg, image/png" multiple>
        </div>

        </div>
        <div class="row my-3 mx-auto justify-content-center">
    <button type="button" class="prev mr-5 btn btn-outline-custom py-0" style="border-radius: 27.5px !important; font-size:15px;" onclick="prevStep(2)">Previous</button>
    <button type="button" onclick="nextStep(2)" style="border-radius: 27.5px !important; font-size:15px;" class="btn-customized">Next</button>
    </div>
</div>
<div class="step" id="step3">
<div class="step3-error-messages"></div>
  <div class="step3-container">
  <div id="medicineItems" >
    <!-- Initial medicine item -->
    <div class="row justify-content-center align-items-end mx-auto pt-4 pb-0 mb-3 medicine-item">

        <div class="col-lg-3 form-group mx-auto p-0 mb-0 pb-0">
            <label for="medicineType">Type of Medicine<span class="red">*</span></label>
            <select name="medicineType[]" class="form-control medicineType" required>
                <option value="" disabled selected>Select Type of Medicine</option>
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
            <label for="medicineGiven">Medicine Given<span class="red">*</span></label>
            <select name="medicineGiven[]" class="form-control medicineGiven" required>
               <option value="" disabled selected>Select Brand</option>
            </select>
            <span class="total-quantity" style="color: gray; position: absolute;
    top: 20;
    right: 0; "></span>
        </div>

        <div class="col-lg-1 form-group mx-auto p-0 mb-0 pb-0">
            <label for="dosageQuantity">Dosage<span class="red">*</span></label>
            <input type="number" name="dosageQuantity[]" class="form-control" placeholder="mL" max="9999" oninput="validateLength(this, 4)" required>
        </div>
        
        <div class="col-lg-1 form-group mx-auto p-0 mb-0 pb-0">
    <label for="route">Route<span class="red">*</span></label>
    <input type="text" name="route[]" placeholder="Route" class="form-control route" readonly>
</div>

        
        <div class="col-lg-1 form-group mx-auto p-0 mb-0 pb-0">
            <label for="quantity">Quantity<span class="red">*</span></label>
            <input type="number" name="quantity[]" class="form-control" placeholder="vl" oninput="validateLength(this, 4)" required>
        </div>
        
        <div class="col-lg-1 mx-auto form-group mb-0 pb-0 mt-auto">
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-add btn-success align-self-end mr-3 addMedicineItem">+</button>
                <button type="button" class="btn btn-add btn-danger align-self-end removeMedicineItem">-</button>
            </div>
        </div>
    </div>
</div>

<div id="equipmentItems">
    <!-- Initial equipment item row -->
    <div class="row mx-auto justify-content-center align-items-end mt-3 equipment-item">
    <div class="col-lg-7 form-group mx-auto mb-0 pb-0 pl-0 mr-3">
        <label for="equipmentType">Type of Equipment<span class="red">*</span></label>
        <select name="equipmentType[]" class="form-control equipmentType" required>
            <option value="" disabled selected>Select Type of Equipment</option>
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
        <label for="equipmentAmount">Equipment Quantity<span class="red">*</span></label>
        <input type="number" name="equipmentAmount[]" class="form-control equipmentAmount" placeholder="Equipment Quantity(pcs)" oninput="validateLength(this, 4)" required>
        <div class="invalid-feedback"></div>
    </div>
    <div class="col-lg-1 mx-auto form-group mb-0 pb-0 mt-auto">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-add btn-success align-self-end mr-3 addEquipmentItem">+</button>
            <button type="button" class="btn btn-add btn-danger align-self-end removeEquipmentItem">-</button>
        </div>
    </div>
</div>
</div>






    <div class="row justify-content-center mx-auto pt-4 pb-0 mb-0">
    <div class="col-lg-4 form-group mx-auto p-0 mb-0 pb-0">
    <label for="treatmentCategory">Treatment Category<span class="red">*</span></label>
    <select id="treatmentCategory" name="treatmentCategory" class="form-control" required readonly>
        <option value="Post-exposure" selected>Post Exposure</option>
    </select>
</div>

        <div class="col-lg-4 form-group mx-auto p-0 mb-0 pb-0">
    <label for="sessions">Sessions<span class="red">*</span></label>
    <select id="sessions" name="sessions" class="form-control" required>
    <option value="" disabled selected>Select Session</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
    </select>
</div>

        <div class="col-lg-3 form-group mx-auto p-0 mb-0 pb-0">
    <label for="treatmentDate">Date of Treatment<span class="red">*</span></label>
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
        <button type="button" class="prev mr-5 btn btn-outline-custom" style="border-radius: 27.5px !important; font-size:15px;" onclick="prevStep(3)">Previous</button>
        <button type="button" class="btn-customized" style="border-radius: 27.5px !important; font-size:15px;"  id="submit-button">Submit</button>
    </div>
</div>
</div>

  </form>
</div>
</div>
</div>  
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <!-- Modal Header -->
                                <div class="modal-header">
                                    <h4 class="modal-title p-3"></h4>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <!-- Modal body -->
                                <div class="modal-body justify-content-center align-items-center d-flex" style="flex-direction:column;">
									<img src="img/img-alerts/caution-mark.png" style="height:50px; width:50px;">
                                                                  <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>INVALID IMAGE</b></h2>
                                <h2 style="letter-spacing: -1px; color:#5e6e82;" class="text-center m-0"><b>FORMAT</b></h2>
                                <div class="text-center">
                                    <small style="letter-spacing: -1px; color:#5e6e82;">Only JPG, JPEG, and PNG can be accepted.<br></small>

                                </div>
                               
                                </div>
                                <!-- Modal footer -->
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

  <!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title p-3" id="reviewModalLabel"></h5>
  
        </button>
      </div>
      <div class="modal-body d-flex flex-column pl-5 pr-4 justify-content-center" id="reviewModalBody">
        <!-- Review content will be dynamically inserted here -->
      </div>
      <div class="modal-footer justify-content-center d-flex" style="border-top:none !important;">
        <button type="button" class="btn gray px-4" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary px-4" style="border-radius:27.5px !important" data-dismiss="modal" id="submitFinal">Submit</button>
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
      <script src="js/notifications.js"> </script>

  <script>
// Function to populate the modal with form data
function getFormData() {
  // Assuming you have a form with id 'multi-step-form' and need to structure data accordingly
  var formData = new FormData(document.getElementById('multi-step-form'));

  // Structure your form data into an object as per your application's needs
  var structuredData = {
    personalInformation: {
      fName: formData.get('fName'),
      mName: formData.get('mName'),
      lName: formData.get('lName'),
      birthDate: formData.get('birthDate'),
      age: formData.get('age'),
      sex: formData.get('sex'),
      weight: formData.get('weight'),
      phoneNumber: formData.get('phoneNumber'),
      email: formData.get('email'),
      address: formData.get('address'),
      province: formData.get('province'),
      city: formData.get('city')
    },
    emergencyContact: {
      emergencyContact: formData.get('emergencyContact'),
      emergency_contact_relationship: formData.get('emergency_contact_relationship'),
      emergencyPhoneNumber: formData.get('emergencyPhoneNumber')
    },
    exposureInformation: {
      exposureDate: formData.get('exposureDate'),
      exposureBy: formData.get('exposureBy'),
      exposureType: formData.get('exposureType'),
      animalType: formData.get('animalType'),
      biteLocation: formData.get('biteLocation'),
      uploadImage: formData.get('uploadImages')
    },
    medicalInformation: {
      medicines: [],
      equipment: []
    },
    treatmentInformation: {
      treatmentCategory: formData.get('treatmentCategory'),
      sessions: formData.get('sessions'),
      treatmentDate: formData.get('treatmentDate'),
      doctorRemarks: formData.get('doctorRemarks')
    }
  };

  // Handle medicines array
  var medicineCount = formData.getAll('medicineType[]').length;
  for (let i = 0; i < medicineCount; i++) {
    var selectedValue = formData.getAll('medicineType[]')[i];
    var medicineTypeElement = document.querySelector(`.medicineType option[value="${selectedValue}"]`);
    var medicineTypeText = medicineTypeElement.textContent;
    var selectedMedicineGivenValue = formData.getAll('medicineGiven[]')[i];
    var selectedMedicineGivenElement = document.querySelector(`.medicineGiven option[value="${selectedMedicineGivenValue}"]`);
    var medicineGivenText = selectedMedicineGivenElement.textContent;

    structuredData.medicalInformation.medicines.push({
      medicineType: medicineTypeText,
      medicineGiven: medicineGivenText,
      dosageQuantity: formData.getAll('dosageQuantity[]')[i],
      route: formData.getAll('route[]')[i],
      quantity: formData.getAll('quantity[]')[i]
    });
  }

  // Handle equipment array
  var equipmentCount = formData.getAll('equipmentType[]').length;
  for (let i = 0; i < equipmentCount; i++) {
    var selectedEquipmentTypeValue = formData.getAll('equipmentType[]')[i];
    var selectedEquipmentTypeElement = document.querySelector(`.equipmentType option[value="${selectedEquipmentTypeValue}"]`);
    var equipmentTypeText = selectedEquipmentTypeElement.textContent;
    structuredData.medicalInformation.equipment.push({
      equipmentType: equipmentTypeText,
      equipmentAmount: formData.getAll('equipmentAmount[]')[i]
    });
  }

  return structuredData;
}

function populateReviewModal() {
  var modalBody = document.getElementById('reviewModalBody');
  var formData = getFormData(); // Get your structured form data here

  var reviewContent = '';

  reviewContent += '<div class="section"><h5 class="text-center font-weight-bold gray">Personal Information</h5> <small>';
  reviewContent += '<ul class="gray">';
  reviewContent += `<li><strong>Name:</strong> ${formData.personalInformation.fName} ${formData.personalInformation.mName} ${formData.personalInformation.lName}</li>`;
  reviewContent += `<li><strong>Birth Date:</strong> ${formData.personalInformation.birthDate}</li>`;
  reviewContent += `<li><strong>Age:</strong> ${formData.personalInformation.age} years</li>`;
  reviewContent += `<li><strong>Sex:</strong> ${formData.personalInformation.sex}</li>`;
  reviewContent += `<li><strong>Weight:</strong> ${formData.personalInformation.weight} kg</li>`;
  reviewContent += `<li><strong>Phone Number:</strong> ${formData.personalInformation.phoneNumber}</li>`;
  reviewContent += `<li><strong>Email:</strong> ${formData.personalInformation.email}</li>`;
  reviewContent += `<li><strong>Address:</strong> ${formData.personalInformation.address}</li>`;
  reviewContent += `<li><strong>Name:</strong> ${formData.emergencyContact.emergencyContact}</li>`;
  reviewContent += `<li><strong>Relationship:</strong> ${formData.emergencyContact.emergency_contact_relationship}</li>`;
  reviewContent += `<li><strong>Phone Number:</strong> ${formData.emergencyContact.emergencyPhoneNumber}</li>`;
  reviewContent += '</ul>';

  reviewContent += '<div class="section" ><h5 class="text-center font-weight-bold gray">Exposure Details</h5>';
  reviewContent += '<ul class="gray">';
  reviewContent += `<li><strong>Date of Exposure:</strong> ${formData.exposureInformation.exposureDate}</li>`;
  reviewContent += `<li><strong>Exposure by:</strong> ${formData.exposureInformation.exposureBy}</li>`;
  reviewContent += `<li><strong>Exposure Type:</strong> ${formData.exposureInformation.exposureType}</li>`;
  reviewContent += `<li><strong>Animal Type:</strong> ${formData.exposureInformation.animalType}</li>`;
  reviewContent += `<li><strong>Bite Location:</strong> ${formData.exposureInformation.biteLocation}</li>`;
  reviewContent += `<li><strong>Upload Image:</strong> ${formData.exposureInformation.uploadImages}</li>`;
  reviewContent += '</ul></div>';

  reviewContent += '<div class="section"><h5 class="text-center font-weight-bold gray">Treatment Given</h5>';
  reviewContent += '<ul class="gray">';
  formData.medicalInformation.medicines.forEach((medicine, index) => {
    reviewContent += `<li><strong>Medicine ${index + 1}:</strong>`;
    reviewContent += `<ul class="gray">`;
    reviewContent += `<li><strong>Type:</strong> ${medicine.medicineType}, <strong>Given:</strong> ${medicine.medicineGiven}, <strong>Dosage:</strong> ${medicine.dosageQuantity} mL, <strong>Route:</strong> ${medicine.route}, <strong>Quantity:</strong> ${medicine.quantity}</li>`;
    reviewContent += `</ul>`;
    reviewContent += `</li>`;
  });
  formData.medicalInformation.equipment.forEach((equipment, index) => {
    reviewContent += `<li><strong>Equipment ${index + 1}:</strong>`;
    reviewContent += `<ul class="gray">`;
    reviewContent += `<li><strong>Type:</strong> ${equipment.equipmentType}, <strong>Amount:</strong> ${equipment.equipmentAmount} pcs</li>`;
    reviewContent += `</ul>`;
    reviewContent += `</li>`;
  });

  reviewContent += `<li><strong>Treatment Category:</strong> ${formData.treatmentInformation.treatmentCategory}</li>`;
  reviewContent += `<li><strong>Number of Sessions:</strong> ${formData.treatmentInformation.sessions}</li>`;
  reviewContent += `<li><strong>Date of Treatment:</strong> ${formData.treatmentInformation.treatmentDate}</li>`;
  reviewContent += `<li><strong>Doctor Remarks:</strong> ${formData.treatmentInformation.doctorRemarks}</li>`;
  reviewContent += '</ul></div> </small>';

  modalBody.innerHTML = reviewContent;
}


// Handling final submit after review



function setMinExposureDate() {
    // Get the value of the birth date
    const birthDate = document.getElementById('birthDate').value;

    if (birthDate) {
        // Calculate 90 days after the birth date
        const minExposureDate = new Date(birthDate);
        minExposureDate.setDate(minExposureDate.getDate() + 90);

        // Format minExposureDate to 'YYYY-MM-DD' for setting min attribute
        const formattedMinExposureDate = minExposureDate.toISOString().split('T')[0];

        // Set the min attribute of the exposureDate input to formattedMinExposureDate
        document.getElementById('exposureDate').min = formattedMinExposureDate;
    }
}

    </script>
  <script>
  document.getElementById("profileDropdown").addEventListener("mousedown", function(event) {
    event.preventDefault(); // Prevent the default action of the mousedown event
    var dropdownContent = document.getElementById("dropdownContent");

    // Check if the clicked element is within the dropdown content
    if (!dropdownContent.contains(event.target)) {
        // Clicked outside the dropdown content, toggle its visibility
        if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
        } else {
            dropdownContent.style.display = "block";
        }
    }
});
</script>


<script>
  // Select all number input fields
const numberInputs = document.querySelectorAll('input[type="number"]');

// Loop through each number input field
numberInputs.forEach(function(input) {
    // Add an event listener for the input event
    input.addEventListener('input', function() {
        // Get the current value of the input field
        let value = parseFloat(this.value);

        // If the value is negative, set it to 0
        if (value < 0 || isNaN(value)) {
            this.value = 0;
        }
    });
});

  // Get the weight input element
const weightInput = document.getElementById('weight');

// Add an event listener for the keydown event
weightInput.addEventListener('keydown', function(event) {
    const keyCode = event.keyCode;
    const inputValue = weightInput.value;
    const decimalIndex = inputValue.indexOf('.');

    // Allow: backspace, delete, tab, escape, enter, and Ctrl+A, home, end, arrow keys
    if (
        [8, 9, 27, 13].includes(keyCode) || // Allow: backspace, delete, tab, escape, enter
        (keyCode === 65 && event.ctrlKey === true) || // Allow: Ctrl+A
        (keyCode >= 35 && keyCode <= 40) // Allow: home, end, left, right, down, up
    ) {
        return;
    }

    // Allow only one decimal point
    if (keyCode === 190 || keyCode === 110) { // Allow: '.' (both key codes for different keyboards)
        if (inputValue.includes('.')) {
            event.preventDefault();
        }
        return;
    }

    // Prevent more than two digits after the decimal point
    if (decimalIndex !== -1 && inputValue.substring(decimalIndex + 1).length >= 2) {
        // Allow backspace and delete to enable correction
        if ([8, 46].includes(keyCode)) {
            return;
        }
        // Prevent further input if two decimal places are already present
        if ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 96 && keyCode <= 105)) {
            event.preventDefault();
        }
    }

    // Allow number keys only
    if ((event.shiftKey || (keyCode < 48 || keyCode > 57)) && (keyCode < 96 || keyCode > 105)) {
        event.preventDefault();
    }
});

// Get the email input element
const emailInput = document.getElementById('email');

// Add an event listener for the keydown event
emailInput.addEventListener('keydown', function(event) {
    // Get the key code of the pressed key
    const keyCode = event.keyCode;

    // If the pressed key is the spacebar, prevent the default behavior
    if (keyCode === 32) {
        event.preventDefault();
    }
});

// Get the age input element
const ageInput = document.getElementById('age');

// Add an event listener for the keydown event
ageInput.addEventListener('keydown', function(event) {
    // Get the key code of the pressed key
    const keyCode = event.keyCode;

    // Allow special keys like backspace, delete, arrow keys, etc.
    if (
        // Allow: backspace, delete, tab, escape, enter
        [8, 9, 27, 13].includes(keyCode) ||
        // Allow: Ctrl+A
        (keyCode === 65 && event.ctrlKey === true) ||
        // Allow: home, end, left, right, down, up
        (keyCode >= 35 && keyCode <= 40)
    ) {
        // Let it happen, don't do anything
        return;
    }

    // Ensure that it is a number and stop the keypress if it isn't
    if ((event.shiftKey || (keyCode < 48 || keyCode > 57)) && (keyCode < 96 || keyCode > 105)) {
        event.preventDefault();
    }
});
// Get the phoneNumber and emergencyPhoneNumber input elements
const phoneNumberInput = document.getElementById('phoneNumber');
const emergencyPhoneNumberInput = document.getElementById('emergencyPhoneNumber');

// Add an event listener for the keydown event on phoneNumber input
phoneNumberInput.addEventListener('keydown', function(event) {
    const keyCode = event.keyCode;

    if (
        [8, 9, 27, 13].includes(keyCode) || // Allow: backspace, delete, tab, escape, enter
        (keyCode === 65 && event.ctrlKey === true) || // Allow: Ctrl+A
        (keyCode >= 35 && keyCode <= 40) // Allow: home, end, left, right, down, up
    ) {
        return;
    }

    if ((event.shiftKey || (keyCode < 48 || keyCode > 57)) && (keyCode < 96 || keyCode > 105)) {
        event.preventDefault();
    }
});

// Add an event listener for the keydown event on emergencyPhoneNumber input
emergencyPhoneNumberInput.addEventListener('keydown', function(event) {
    const keyCode = event.keyCode;

    if (
        [8, 9, 27, 13].includes(keyCode) || // Allow: backspace, delete, tab, escape, enter
        (keyCode === 65 && event.ctrlKey === true) || // Allow: Ctrl+A
        (keyCode >= 35 && keyCode <= 40) // Allow: home, end, left, right, down, up
    ) {
        return;
    }

    if ((event.shiftKey || (keyCode < 48 || keyCode > 57)) && (keyCode < 96 || keyCode > 105)) {
        event.preventDefault();
    }
});




document.addEventListener("DOMContentLoaded", function() {
    var animalTypeInput = document.getElementById("otherAnimalType");
    var biteLocationInput = document.getElementById("biteLocation");

    // Function to validate input and allow only letters and spaces
    function validateAnimalTypeInput(inputField) {
        inputField.addEventListener("keypress", function(event) {
            var keyCode = event.keyCode;
            // Allow letters (a-z, A-Z) and spaces (32)
            if ((keyCode >= 65 && keyCode <= 90) || // A-Z
                (keyCode >= 97 && keyCode <= 122) || // a-z
                keyCode === 32) { // space
                return true;
            } else {
                event.preventDefault();
                return false;
            }
        });
    }

    // Function to validate input and allow letters, spaces, and comma
    function validateBiteLocationInput(inputField) {
        inputField.addEventListener("keypress", function(event) {
            var keyCode = event.keyCode;
            // Allow letters (a-z, A-Z), spaces (32), and comma (44)
            if ((keyCode >= 65 && keyCode <= 90) || // A-Z
                (keyCode >= 97 && keyCode <= 122) || // a-z
                keyCode === 32 || // space
                keyCode === 44) { // comma
                return true;
            } else {
                event.preventDefault();
                return false;
            }
        });
    }

    // Apply input validation to animalType and biteLocation inputs
    validateAnimalTypeInput(animalTypeInput);
    validateBiteLocationInput(biteLocationInput);
});

function toggleCustomSuffixInput(value) {
  const customSuffixInput = document.getElementById('customSuffix');
  if (value === 'Other') {
    customSuffixInput.style.display = 'block';
    customSuffixInput.required = true;
  } else {
    customSuffixInput.style.display = 'none';
    customSuffixInput.value = '';
    customSuffixInput.required = false;
  }
}



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
    validateFirstNameField();
    validateMiddleNameField();
    validateLastNameField();
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
    if (currentStep === 2) {
            $('.step3-error-messages').empty();
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
// Function to validate Step 3 fields
function validateStep3Fields() {
    const errorContainer = document.querySelector('.step3-error-messages');
    errorContainer.innerHTML = '';

    const inputFields = document.querySelectorAll('.step3-container input, .step3-container select, .step3-container textarea');
    inputFields.forEach(function(field) {
        field.classList.remove('error-border');
    });

    const requiredFields = document.querySelectorAll('.step3-container input[required], .step3-container select[required], .step3-container textarea[required]');
    const missingFields = [];

    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            missingFields.push(field);
        }
    });

    if (missingFields.length > 0) {
        const errorMessage = document.createElement('div');
        errorMessage.classList.add('error-message');
        errorMessage.textContent = 'Please fill in all required fields correctly.';
        errorContainer.appendChild(errorMessage);
        
        missingFields.forEach(function(field) {
            field.classList.add('error-border');
        });
        
        return false; // Validation failed
    }
    
    return true; // Validation passed
}


$(document).ready(function () {

  function fetchBrandsAndQuantity(medicineTypeDropdown, medicineGivenDropdown, totalQuantityContainer) {
    const medicineId = medicineTypeDropdown.val();
    console.log("Sending AJAX request with medicineType:", medicineId);

    $.ajax({
        url: 'backend/fetch-brands.php',
        method: 'POST',
        data: { medicineType: medicineId },
        dataType: 'json',
        success: function (response) {
            medicineGivenDropdown.empty();

            // Create the default option
            medicineGivenDropdown.append(`<option value="" selected disabled >Select Brand</option>`);
            // Populate the dropdown with brands from the response
            $.each(response, function (index, value) {
         
                if (!medicineGivenDropdown.find(`option[value="${value.MedicineBrandID}"]`).length) {
                    medicineGivenDropdown.append(`<option value="${value.MedicineBrandID}">${value.BrandName}</option>`);
                }
            });

            updateDropdownOptions();
        },
        error: function (xhr) {
            console.error(xhr.responseText);
        }
    });



        // Event listener for the change event on medicine given dropdown
        medicineGivenDropdown.off('change').on('change', function () {
            const routeInput = $(this).closest('.medicine-item').find('.route');
            const selectedBrandID = $(this).val();
            fetchQuantity(selectedBrandID, totalQuantityContainer, routeInput);
            updateDropdownOptions();
        });
    }

    // Function to fetch quantity based on selected brand
    function fetchQuantity(medicineBrandID, totalQuantityContainer, routeInput) {
        $.ajax({
            url: 'backend/fetch-quantity.php',
            method: 'POST',
            data: { medicineBrandID: medicineBrandID },
            dataType: 'json',
            success: function (response) {
                totalQuantityContainer.text(`Total Quantity: ${response.TotalQuantity}`);
                routeInput.val(response.Route);
                validateQuantity(totalQuantityContainer);
            },
            error: function (xhr) {
                console.error(xhr.responseText);
            }
        });
    }

    // Function to validate quantity input for a specific medicine item
    function validateQuantity(inputField, totalQuantity) {
    const enteredQuantity = parseInt(inputField.val());
    const invalidFeedback = inputField.closest('.form-group').find('.invalid-feedback');

    if (isNaN(enteredQuantity) || enteredQuantity < 1 || enteredQuantity > totalQuantity) {
        inputField.addClass('is-invalid');
        const message = `Quantity must be between 1 and ${totalQuantity}`;
        invalidFeedback.text(message);

        // Add data attributes for tooltip
        inputField.attr('data-toggle', 'tooltip');
        inputField.attr('data-placement', 'right');
        inputField.attr('title', message);

        // Initialize tooltip
        inputField.tooltip({ trigger: 'hover' });
    } else {
        inputField.removeClass('is-invalid');
        invalidFeedback.text('');

        // Remove tooltip
        inputField.removeAttr('data-toggle data-placement title');
        inputField.tooltip('dispose');
    }
}


    // Function to apply input validation to a specific medicine item
    function applyInputValidation(element) {
        element.find('input[name="quantity[]"]').each(function () {
            const inputField = $(this);
            const totalQuantityContainer = element.find('.total-quantity');
            const totalQuantity = parseInt(totalQuantityContainer.text().replace('Total Quantity: ', ''));
            validateQuantity(inputField, totalQuantity);
        });
    }

    // Function to add a new medicine item
    function addMedicineItem() {
        const newMedicineItem = $('#medicineItems .medicine-item').first().clone();
        newMedicineItem.find('input').val('');
        newMedicineItem.find('.total-quantity').text('Total Quantity: ');

        newMedicineItem.appendTo('#medicineItems');
        newMedicineItem.find('.medicineType').trigger('change');
        applyInputValidation(newMedicineItem);
    }

    // Function to add a new equipment item
    function addEquipmentItem() {
        const newEquipmentItem = $('#equipmentItems .equipment-item').first().clone();
        newEquipmentItem.find('input').val('');
        newEquipmentItem.find('input').val('').removeClass('is-invalid'); // Clear input and remove validation class
        newEquipmentItem.find('.invalid-feedback').text(''); // Clear validation message
        newEquipmentItem.appendTo('#equipmentItems');
        applyInputValidation(newEquipmentItem);
    }

    // Function to update dropdown options to prevent duplicate selection
    function updateDropdownOptions() {
    const selectedOptions = [];
    $('.medicineGiven').each(function () {
        const selectedValue = $(this).val();
        if (selectedValue) {
            selectedOptions.push(selectedValue);
        }
    });

    $('.medicineGiven').each(function () {
        const currentDropdown = $(this);
        currentDropdown.find('option').each(function () {
            const optionValue = $(this).val();
            if (optionValue === '') {
                // Ensure the default "Select Brand" option remains disabled
                $(this).prop('disabled', true);
            } else if (selectedOptions.includes(optionValue) && optionValue !== currentDropdown.val()) {
                $(this).attr('disabled', 'disabled');
            } else {
                $(this).removeAttr('disabled');
            }
        });
    });
}


    // Event listener for the change event on medicine type dropdown
    $(document).on('change', '.medicineType', function () {
        const medicineItem = $(this).closest('.medicine-item');
        const medicineGivenDropdown = medicineItem.find('.medicineGiven');
        const totalQuantityContainer = medicineItem.find('.total-quantity');
        fetchBrandsAndQuantity($(this), medicineGivenDropdown, totalQuantityContainer);
    });

    // Event listener for the input event on quantity input
    $(document).on('input', 'input[name="quantity[]"]', function () {
        const inputField = $(this);
        const medicineItem = inputField.closest('.medicine-item');
        const totalQuantityContainer = medicineItem.find('.total-quantity');
        const totalQuantity = parseInt(totalQuantityContainer.text().replace('Total Quantity: ', ''));
        validateQuantity(inputField, totalQuantity);
    });

    // Function to apply input validation event listeners
    function applyInputValidation(element) {
        const allowNumericInput = (event) => {
            const key = event.key;
            if (!/[0-9.]/.test(key) && key !== 'Backspace' && key !== 'Delete' && !['ArrowLeft', 'ArrowRight'].includes(key)) {
                event.preventDefault();
            }
        };

        element.find('input[name="dosageQuantity[]"], input[name="quantity[]"], input[name="equipmentAmount[]"]').on('keydown', allowNumericInput);

        element.find('input[type="number"]').on('input', function () {
            if (this.value < 0 || isNaN(this.value)) {
              this.value = '1';
            }
        });
    }
    $(document).ready(function () {

      $(document).ready(function () {
    // Event listener for the input event on dosage quantity input
    $(document).on('input', 'input[name="dosageQuantity[]"]', function () {
        const inputField = $(this);
        let enteredValue = inputField.val().trim(); // Trim whitespace

        // Convert entered value to float
        let floatValue = parseFloat(enteredValue);

        // Check if the entered value is empty
        if (enteredValue === '') {
            // Allow the field to be empty
            inputField.removeClass('is-invalid');
            return;
        } else if (isNaN(floatValue) || floatValue < 0.3) {
            // Set the value to 0.3 if it is lower than 0.3 or not a number
            inputField.val(0.3);
            floatValue = 0.3;
        }

        // Validate if the value is less than 0.3
        if (floatValue < 0.3) {
            inputField.val(0.3);
        }
    });

    // Event listener for the input event on quantity input
    $(document).on('input', 'input[name="quantity[]"]', function () {
        const inputField = $(this);
        let enteredValue = inputField.val().trim(); // Trim whitespace

        // Convert entered value to integer
        let intValue = parseInt(enteredValue);

        // Check if the entered value is empty
        if (enteredValue === '') {
            // Allow the field to be empty
            inputField.removeClass('is-invalid');
            return;
        } else if (isNaN(intValue) || intValue < 1) {
            // Set the value to 1 if it is lower than 1 or not a number
            inputField.val(1);
            intValue = 1;
        }

        // Validate if the value is less than 1
        if (intValue < 1) {
            inputField.val(1);
        }
    });
});


});

    // Event listener for the "Add Medicine" button
    $(document).on('click', '.addMedicineItem', addMedicineItem);

    // Event listener for the "Add Equipment" button
    $(document).on('click', '.addEquipmentItem', addEquipmentItem);

    // Event listener for the "Remove Equipment" button
    $(document).on('click', '.removeEquipmentItem', function () {
        if ($('.equipment-item').length > 1) {
            $(this).closest('.equipment-item').remove();
            updateDropdownOptions();
        } else {
            alert("At least one equipment item is required.");
        }
    });

    // Event listener for the "Remove Medicine" button
    $(document).on('click', '.removeMedicineItem', function () {
        if ($('.medicine-item').length > 1) {
            $(this).closest('.medicine-item').remove();
            updateDropdownOptions();
        } else {
            alert("At least one medicine item is required.");
        }
    });

    // Function to fetch equipment stock based on selected equipment type
    function fetchEquipmentStock(equipmentTypeDropdown, equipmentAmountInput) {
        const equipmentId = equipmentTypeDropdown.val();
        if (equipmentId) {
            $.ajax({
                url: 'backend/fetch-equipment-stock.php', // Path to your PHP script to fetch equipment stock
                method: 'POST',
                data: { equipmentID: equipmentId },
                dataType: 'json',
                success: function (response) {
                    const totalStock = response.TotalStock;
                    equipmentAmountInput.data('totalStock', totalStock); // Store the total stock in a data attribute
                    validateEquipmentAmount(equipmentAmountInput, totalStock);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                }
            });
        }
    }

    // Function to validate equipment amount input
    function validateEquipmentAmount(inputField, totalStock) {
        const enteredAmount = parseInt(inputField.val());
        const invalidFeedback = inputField.closest('.form-group').find('.invalid-feedback');

        if (isNaN(enteredAmount) || enteredAmount < 1 || enteredAmount > totalStock) {
            inputField.addClass('is-invalid');
            invalidFeedback.text(`Amount must be between 1 and ${totalStock}`);
        } else {
            inputField.removeClass('is-invalid');
            invalidFeedback.text('');
        }
    }

    // Event listener for the change event on equipment type dropdown
    $(document).on('change', '.equipmentType', function () {
        const equipmentItem = $(this).closest('.equipment-item');
        const equipmentAmountInput = equipmentItem.find('.equipmentAmount');
        fetchEquipmentStock($(this), equipmentAmountInput);
    });

    // Event listener for the input event on equipment amount input
    $(document).on('input', '.equipmentAmount', function () {
        const inputField = $(this);
        const totalStock = inputField.data('totalStock');
        validateEquipmentAmount(inputField, totalStock);
    });

    // Function to validate all equipment and medicine fields
    function validateAllFields() {
        let isValid = true;

        // Validate equipment fields
        $('.equipment-item').each(function () {
            const equipmentAmountInput = $(this).find('.equipmentAmount');
            const totalStock = equipmentAmountInput.data('totalStock');
            if (totalStock !== undefined) {
                validateEquipmentAmount(equipmentAmountInput, totalStock);
                if (equipmentAmountInput.hasClass('is-invalid')) {
                    isValid = false;
                }
            }
        });

        // Validate medicine fields
        $('.medicine-item').each(function () {
            const quantityInput = $(this).find('input[name="quantity[]"]');
            const totalQuantityContainer = $(this).find('.total-quantity');
            const totalQuantity = parseInt(totalQuantityContainer.text().replace('Total Quantity: ', ''));
            validateQuantity(quantityInput, totalQuantity);
            if (quantityInput.hasClass('is-invalid')) {
                isValid = false;
            }
        });

        return isValid;
    }
    document.getElementById('submit-button').addEventListener('click', function(e) {
    e.preventDefault();

    // Validate Step 2 and Step 3 fields
    var step2Valid = validateStep2Fields();
    var step3Valid = validateStep3Fields();
    var allFieldsValid = validateAllFields();

    // Check if all validations passed
    if (step2Valid && step3Valid && allFieldsValid) {
        // Show the review modal
        populateReviewModal();
        $('#reviewModal').modal('show');

        // Get the submitFinal button
        var submitFinalButton = document.getElementById('submitFinal');

        // Remove any existing event listener
        submitFinalButton.removeEventListener('click', submitFinalHandler);

        // Attach the AJAX call to the submitFinal button
        submitFinalButton.addEventListener('click', submitFinalHandler);
    } else {
        console.log('Please fill in all required fields correctly.');
    }
});

// Define the event handler function separately
function submitFinalHandler() {
    const formData = new FormData(document.getElementById('multi-step-form'));

    $.ajax({
        url: 'backend/submit.php',
        method: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(response) {
            console.log("Success response:", response);
            if (response.status === 'success') {
                console.log("Form submitted successfully. Redirecting to patient-list.php...");
                sessionStorage.removeItem('formData');
                window.location.href = 'patient-list.php';
            } else {
                console.log("Form submission error: " + response.message);
                $('#responseMessage').text(response.message);
                $('#responseModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error response:", xhr.responseText);
            $('#responseMessage').text('An error occurred while processing your request. Please try again.');
            $('#responseModal').modal('show');
        }
    });
}



});
function validateStep2Fields() {
    const errorContainer = document.querySelector('.step3-error-messages');
    errorContainer.innerHTML = '';
    let step2Valid = true;

    // Implement your validation logic for Step 2 fields here
    $('#step2 input[required], #step2 select[required]').each(function() {
        if (!$(this).val().trim()) {
            step2Valid = false;
            const errorMessage = document.createElement('div');
            errorMessage.classList.add('error-message');
            errorMessage.textContent = 'Please return to Step 2 for Post-exposure.';
            errorContainer.appendChild(errorMessage);
        }
    });

    return step2Valid;
}

function validateAllFields() {
        var allFieldsValid = true;
        // Implement your validation logic for all fields here
        // Example: Check if all required fields across the form are filled
        $('#multi-step-form input[required], #multi-step-form select[required]').each(function() {
            if (!$(this).val().trim()) {
                allFieldsValid = false;
                return false; // Exit loop early if an invalid field is found
            }
        });
        return allFieldsValid;
    }
    function saveFormData() {
    const formData = {};
    $('#step1, #step2').find('input, select, textarea').each(function () {
        formData[$(this).attr('name')] = $(this).val();
    });
    sessionStorage.setItem('formData', JSON.stringify(formData));
}


    // Function to load form data from sessionStorage
    function loadFormData() {
    const formData = JSON.parse(sessionStorage.getItem('formData'));
    if (formData) {
        $('#step1, #step2').find('input, select, textarea').each(function () {
            if (formData[$(this).attr('name')]) {
                $(this).val(formData[$(this).attr('name')]);
            }
        });
    }
}


// Save form data on input change within step1 and step2
$('#step1, #step2').on('input change', 'input, select, textarea', function () {
    saveFormData();
});

// Load form data on page load for step1 and step2
$(document).ready(function() {
    loadFormData();
});





// Define JavaScript variable to hold PHP-generated data
var provincesAndCities = <?php echo json_encode($provincesAndCities); ?>;

// Get references to the province and city select elements
var provinceSelect = document.getElementById("provinceSelect");
var citySelect = document.getElementById("citySelect");

// Add event listener to the province select element
provinceSelect.addEventListener("change", function() {
    // Clear previous options in the city select element
    citySelect.innerHTML = "<option value='' disabled selected>Select City</option>";
    
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

function validateBirthDate(birthDate) {
  const currentDate = new Date();
  const selectedDate = new Date(birthDate);

  // Calculate 1 day before the current date
  const oneDayBefore = new Date(currentDate);
  oneDayBefore.setDate(currentDate.getDate() - 1);

  // Check if selectedDate is at least 1 day before the current date
  return selectedDate < oneDayBefore;
}



  // Function to validate age
  function validateAge(age) {
    return age >= 0 && age <= 124;
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
    return weight >= 1 && weight <= 650;
  }

  let validDomains = []; // Array to store valid domains
      let existingEmails = []; // Array to store existing emails

      // Fetch valid domains from CSV file
      fetch('free-domains-2.csv')
        .then(response => response.text())
        .then(csvData => {
          validDomains = csvData.split('\n').map(domain => domain.trim());
          console.log('Valid domains:', validDomains); // Debug log to check valid domains
        })
        .catch(error => console.error('Error fetching CSV file:', error));

      // Fetch existing emails from the server
      fetch('fetch_existing_emails.php')
        .then(response => response.json())
        .then(data => {
          existingEmails = data.map(emailObj => emailObj.EmailAddress);
          console.log('Existing emails:', existingEmails); // Debug log to check existing emails
        })
        .catch(error => console.error('Error fetching existing emails:', error));

      // Function to validate email format and domain
      function validateEmail(email) {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const emailDomain = email.split('@')[1];

        if (!emailPattern.test(email)) {
          return 'Invalid email format.';
        } else if (!validDomains.includes(emailDomain)) {
          return 'Please enter an email with a known domain.';
        } else if (existingEmails.includes(email)) {
          return 'This email address is already in use.';
        } else {
          return ''; // Email is valid
        }
      }





// Get the email input element

  // Function to display error message and apply red border
// Function to display error message and apply red border
function showError(field, message) {
  if (field.id === 'phoneNumber' || field.id === 'emergencyPhoneNumber') {
    const errorElementId = field.id === 'phoneNumber' ? 'phone-number-error' : 'emergency-phone-number-error';
    const errorElement = document.getElementById(errorElementId);
    if (errorElement) {
      errorElement.textContent = message;
    }
    field.classList.add('error-border');
  } else {
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
}




// Function to remove error message and red border
function removeError(field) {
  if (field.id === 'phoneNumber') {
    const errorContainer = document.getElementById('phone-number-error');
    if (errorContainer) {
      errorContainer.textContent = '';
    }
  } else if (field.id === 'emergencyPhoneNumber') {
    const errorContainer = document.getElementById('emergency-phone-number-error');
    if (errorContainer) {
      errorContainer.textContent = '';
    }
  } else {
    const errorContainer = field.nextElementSibling;
    if (errorContainer && errorContainer.classList.contains('error-message')) {
      field.parentNode.removeChild(errorContainer);
    }
  }
  field.classList.remove('error');
  field.classList.remove('error-border');
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
      showError(document.getElementById('age'), 'Age should be between 1 and 124.');
    } else {
      removeError(document.getElementById('age'));
    }
  }

  // Function to validate phone number field
  function validatePhoneNumberField() {
  const phoneNumber = document.getElementById('phoneNumber').value.trim();
  const isValid = validatePhoneNumber(phoneNumber);
  if (!isValid && phoneNumber !== '') {
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
  const weightElement = document.getElementById('weight');
  const weightValue = weightElement.value.trim();
  const weight = parseFloat(weightValue);
  const isValid = validateWeight(weight);

  if (!isValid && weightValue !== '') {
    showError(weightElement, 'Weight should be between 1 and 650 kg.');
  } else {
    removeError(weightElement);
  }
}


  // Function to validate email field
  function validateEmailField() {
  const emailField = document.getElementById('email');
  const email = emailField.value.trim(); // Trimmed email value

  const errorMessage = validateEmail(email); // Validate email format and domain
  if (errorMessage) {
    showError(emailField, errorMessage); // Show error message
  } else {
    removeError(emailField); // Remove error styling
  }
}

  function validateFirstNameField() {
  const fName = document.getElementById('fName').value.trim();
  const errorMessage = validateName(fName);
  if (errorMessage && fName !== '') {
    showError(document.getElementById('fName'), errorMessage);
  } else {
    removeError(document.getElementById('fName'));
  }
}
function validateEmergencyContactField() {
    const emergencyContact = document.getElementById('emergencyContact').value.trim();
    const errorMessage = validateName(emergencyContact);
    if (errorMessage && emergencyContact !== '') {
        showError(document.getElementById('emergencyContact'), errorMessage);
    } else {
        removeError(document.getElementById('emergencyContact'));
    }
}

// Function to validate middle name field
function validateMiddleNameField() {
  const mName = document.getElementById('mName').value.trim();
  const errorMessage = validateName(mName);
  if (errorMessage && mName !== '') { // middle name can be optional
    showError(document.getElementById('mName'), errorMessage);
  } else {
    removeError(document.getElementById('mName'));
  }
}

// Function to validate last name field
function validateLastNameField() {
  const lName = document.getElementById('lName').value.trim();
  const errorMessage = validateName(lName);
  if (errorMessage && lName !== '') {
    showError(document.getElementById('lName'), errorMessage);
  } else {
    removeError(document.getElementById('lName'));
  }
}

// Function to validate name fields
function validateName(name) {
  const namePattern = /^[A-Za-zÀ-ÖØ-öø-ÿ]+(?:[ '-][A-Za-zÀ-ÖØ-öø-ÿ]+)*$/; // Allows letters, spaces, hyphens, and apostrophes
  const repeatingCharsPattern = /(.)\1{2,}/; // Matches any character repeated 3 or more times

  if (!namePattern.test(name)) {
    return 'Names should only contain letters, spaces, hyphens, and apostrophes.';
  } else if (repeatingCharsPattern.test(name)) {
    return 'Names should not contain repeating characters.';
  } else if (/[A-Z]{2,}/.test(name) || (/^[a-z]/.test(name) && /[A-Z]/.test(name.substring(1)))) {
    return 'Names should not have unusual capitalization.';
  } else {
    return '';
  }
}

  // Add event listeners to input fields to validate them as the user types
  document.getElementById('birthDate').addEventListener('input', validateBirthDateField);
  document.getElementById('age').addEventListener('input', validateAgeField);
  document.getElementById('weight').addEventListener('input', validateWeightField);
  document.getElementById('fName').addEventListener('input', validateFirstNameField);
  document.getElementById('emergencyContact').addEventListener('input', validateEmergencyContactField);
  document.getElementById('mName').addEventListener('input', validateMiddleNameField);
  document.getElementById('lName').addEventListener('input', validateLastNameField);
  
     $('#phoneNumber').on('input', function() {
        validatePhoneNumberField();
    });
    $('#emergencyPhoneNumber').on('input', function() {
        validateEmergencyPhoneNumberField();
    });
    $('#email').on('input', function() {
      validateEmailField();
  });

</script>
<script>
function preventLeadingSpace(event) {
    const input = event.target;
    if (input.value.startsWith(' ')) {
        input.value = input.value.trim(); // Remove leading space
    }
    // Replace multiple consecutive spaces with a single space
    input.value = input.value.replace(/\s{2,}/g, ' ');
}

function preventSpaces(event) {
        const input = event.target;
        if (input.value.includes(' ')) {
            input.value = input.value.replace(/\s/g, ''); // Remove all spaces
        }
    }




</script>
<!-- Existing JavaScript and closing body tag -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const phoneNumberInput = document.getElementById('phoneNumber');

    phoneNumberInput.addEventListener('input', function() {
        let phoneNumber = phoneNumberInput.value.trim();

        // Check if the number does not start with '09'
        if (!phoneNumber.startsWith('09')) {
            phoneNumber = '09' + phoneNumber.substring(2); // Prepend '09' and keep the rest of the input
            phoneNumberInput.value = phoneNumber; // Update the input value
        }
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const emergencyPhoneNumberInput = document.getElementById('emergencyPhoneNumber');

    emergencyPhoneNumberInput.addEventListener('input', function() {
        let phoneNumber = emergencyPhoneNumberInput.value.trim();

        // Check if the number does not start with '09'
        if (!phoneNumber.startsWith('09')) {
            phoneNumber = '09' + phoneNumber.substring(2); // Prepend '09' and keep the rest of the input
            emergencyPhoneNumberInput.value = phoneNumber; // Update the input value
        }
    });
});
// Add event listeners to input fields to capitalize them as the user types
document.getElementById('fName').addEventListener('input', function() {
    const nameInput = this;
    let capitalized = capitalizeFirstLetter(nameInput.value);
    nameInput.value = capitalized;
});

document.getElementById('mName').addEventListener('input', function() {
    const nameInput = this;
    let capitalized = capitalizeFirstLetter(nameInput.value);
    nameInput.value = capitalized;
});

document.getElementById('lName').addEventListener('input', function() {
    const nameInput = this;
    let capitalized = capitalizeFirstLetter(nameInput.value);
    nameInput.value = capitalized;
});

document.getElementById('emergencyContact').addEventListener('input', function() {
    const nameInput = this;
    let capitalized = capitalizeFirstLetter(nameInput.value);
    nameInput.value = capitalized;
});

// Function to capitalize the first letter of each word in a string
function capitalizeFirstLetter(str) {
    return str.split(' ').map(word => {
        if (word) {
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        }
        return '';
    }).join(' ');
}
document.getElementById('uploadImages').addEventListener('change', function() {
    const input = this;
    const files = input.files;

    // Regular expression to check file extension
    const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        if (!allowedExtensions.test(file.name)) {
            showError(input, 'Only JPEG, PNG, and JPG files are allowed.');
            input.value = ''; // Clear the input field
            return;
        }
    }

    removeError(input);
});


</script>
<script>
    // Function to handle selection change and show/hide the other input field
    function checkOtherOption() {
        const animalTypeSelect = document.getElementById('animalType');
        const otherAnimalTypeInput = document.getElementById('otherAnimalType');

        if (animalTypeSelect.value === 'Other') {
            otherAnimalTypeInput.classList.remove('d-none');
            otherAnimalTypeInput.focus(); // Focus on the input field for user convenience
        } else {
            otherAnimalTypeInput.classList.add('d-none');
            otherAnimalTypeInput.value = ''; // Clear the input field if it was previously filled
        }
    }
</script>
<script>
function validateLength(element, maxLength) {
    if (element.value.length > maxLength) {
        element.value = element.value.slice(0, maxLength);
    }
}
</script>
</body>
</html>
