<?php
// plu-sin.php - last updated March 2010
// Author: Nick D. <global@w-global.com>
// Copyright (C) 2010
// See LICENSE.txt for details.

// Based on "An Algorithmic Approach to English Pluralization" by Damian Conway:
// http://www.csse.monash.edu.au/~damian/papers/HTML/Plurals.html ,
// Based on Tom De Smedt <tomdesmedt@organisms.be> python source code.
// Based on  Bermi Ferrer's Inflector for Python: // http://www.bermi.org/inflector/

// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software to deal in this software without restriction, including
// without limitation the rights to use, copy, modify, merge, publish,
// distribute, sublicense, and/or sell copies of this software, and to permit
// persons to whom this software is furnished to do so, subject to the following
// condition:
//
// THIS SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THIS SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THIS SOFTWARE.
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

class Pluralizer {
	
	static $plural_prepositions = array("about", "above", "across", "after", "among", "around", "at", "athwart", "before", "behind", "below", "beneath", "beside", "besides", "between", "betwixt", "beyond", "but", "by", "during", "except", "for", "from", "in", "into", "near", "of", "off", "on", "onto", "out", "over", "since", "till", "to", "under", "until", "unto", "upon", "with");

	// Inflection rules that are either general,
	// or apply to a certain category of words,
	// or apply to a certain category of words only in classical mode,
	// or apply only in classical mode.
	
	// Each rule consists of:
	// suffix, inflection, category and classic flag.
	static $plural_rules = array(
		array(
		array("^a$|^an$", "some", "none", "false"),
		array("^this$", "these", "none", "false"),
		array("^that$", "those", "none", "false"),
		array("^any$", "all", "none", "false")
		),
		array(
		array("^my$", "our", "none", "false"),
		array("^your$|^thy$", "your", "none", "false"),
		array("^her$|^his$|^its$|^their$", "their", "none", "false")
		),
		array(
		array("^mine$", "ours", "none", "false"),
		array("^yours$|^thine$", "yours", "none", "false"),
		array("^hers$|^his$|^its$|^theirs$", "theirs", "none", "false")
		),   
		array(
		array("^I$", "we", "none", "false"),
		array("^me$", "us", "none", "false"),
		array("^myself$", "ourselves", "none", "false"),
		array("^you$", "you", "none", "false"),
		array("^thou$|^thee$", "ye", "none", "false"),
		array("^yourself$|^thyself$", "yourself", "none", "false"),
		array("^she$|^he$|^it$|^they$", "they", "none", "false"),
		array("^her$|^him$|^it$|^them$", "them", "none", "false"),
		array("^herself$|^himself$|^itself$|^themself$", "themselves", "none", "false"),
		array("^oneself$", "oneselves", "none", "false")
		),
		array(
		array("$", "", "0", "false"),
		array("$", "", 1, "false"),
		array("s$", "s", 2, "false"),
		array("fish$", "fish", "none", "false"),
		array("([- ])bass$", "\\1bass", "none", "false"),
		array("ois$", "ois", "none", "false"),
		array("sheep$", "sheep", "none", "false"),
		array("deer$", "deer", "none", "false"),
		array("pox$", "pox", "none", "false"),
		array("([A-Z].*)ese$", "\\1ese", "none", "false"),
		array("itis$", "itis", "none", "false"),
		array("(fruct|gluc|galact|lact|ket|malt|rib|sacchar|cellul)ose$", "\\1ose", "none", "false")
		),
		array(
		array("atlas$", "atlantes", "none", "true"),
		array("atlas$", "atlases", "none", "false"),
		array("beef$", "beeves", "none", "true"),
		array("brother$", "brethren", "none", "true"),
		array("child$", "children", "none", "false"),
		array("corpus$", "corpora", "none", "true"),
		array("corpus$", "corpuses", "none", "false"),
		array("^cow$", "kine", "none", "true"),
		array("ephemeris$", "ephemerides", "none", "false"),
		array("ganglion$", "ganglia", "none", "true"),
		array("genie$", "genii", "none", "true"),
		array("genus$", "genera", "none", "false"),
		array("graffito$", "graffiti", "none", "false"),
		array("loaf$", "loaves", "none", "false"),
		array("money$", "monies", "none", "true"),
		array("mongoose$", "mongooses", "none", "false"),
		array("mythos$", "mythoi", "none", "false"),
		array("octopus$", "octopodes", "none", "true"),
		array("opus$", "opera", "none", "true"),
		array("opus$", "opuses", "none", "false"),
		array("^ox$", "oxen", "none", "false"),
		array("penis$", "penes", "none", "true"),
		array("penis$", "penises", "none", "false"),
		array("soliloquy$", "soliloquies", "none", "false"),
		array("testis$", "testes", "none", "false"),
		array("trilby$", "trilbys", "none", "false"),
		array("turf$", "turves", "none", "true"),
		array("numen$", "numena", "none", "false"),
		array("occiput$", "occipita", "none", "true"),
		),
		array(
		array("man$", "men", "none", "false"),
		array("person$", "people", "none", "false"),
		array("([lm])ouse$", "\\1ice", "none", "false"),
		array("tooth$", "teeth", "none", "false"),
		array("goose$", "geese", "none", "false"),
		array("foot$", "feet", "none", "false"),
		array("zoon$", "zoa", "none", "false"),
		array("([csx])is$", "\\1es", "none", "false")
		),
		array(
		array("ex$", "ices", 3, "false"),
		array("ex$", "ices", 4, "true"),
		array("um$", "a", 5, "false"),
		array("um$", "a", 6, "true"),
		array("on$", "a", 7, "false"),
		array("a$", "ae", 8, "false"),
		array("a$", "ae", 9, "true")
		),
		array(
		array("trix$", "trices", "none", "true"),
		array("eau$", "eaux", "none", "true"),
		array("ieu$", "ieu", "none", "true"),
		array("([iay])nx$", "\\1nges", "none", "true"),
		array("en$", "ina", 10, "true"),
		array("a$", "ata", 11, "true"),
		array("is$", "ides", 12, "true"),
		array("us$", "i", 13, "true"),
		array("us$", "us", 14, "true"),
		array("o$", "i", 15, "true"),
		array("$", "i", 16, "true"),
		array("$", "im", 17, "true")
		),	 
		array(
		array("([cs])h$", "\\1hes", "none", "false"),
		array("ss$", "sses", "none", "false"),
		array("x$", "xes", "none", "false")
		),
		array(
		array("([aeo]l)f$", "\\1ves", "none", "false"),
		array("([^d]ea)f$", "\\1ves", "none", "false"),
		array("arf$", "arves", "none", "false"),
		array("([nlw]i)fe$", "\\1ves", "none", "false"),
		),
		array(
		array("([^aeiouy]|qu)y$", "$1ies", "none", "false"),
		array("([aeiou])y$", "\\1ys", "none", "false"),
		array("([A-Z].*)y$", "\\1ys", "none", "false"),
		array("y$", "ies", "none", "false")
		),	
		array(
		array("o$", "os", 18, "false"),
		array("([aeiou])o$", "\\1os", "none", "false"),
		array("o$", "oes", "none", "false")
		),
		array(
		array("l$", "ls", 19, "false")
		),
		array(
		array("$", "s", "none", "false")
	),
	);
	
	// Suffix categories

	static $plural_categories = array(
		 array("uninflected", array("bison", "bream", "breeches", "britches", "carp", "chassis", "clippers", "cod", "contretemps", "corps", "debris", "diabetes", "djinn", "eland", "elk", "flounder", "gallows", "graffiti", "headquarters", "herpes", "high-jinks", "homework", "innings", "jackanapes", "mackerel", "measles", "mews", "mumps", "news", "pincers", "pliers", "proceedings", "rabies", "salmon", "scissors", "series", "shears", "species", "swine", "trout", "tuna", "whiting", "wildebeest")),
		 array("uncountable", array("advice", "bread", "butter", "cheese", "electricity", "equipment", "fruit", "furniture", "garbage", "gravel", "happiness", "information", "ketchup", "knowledge", "love", "luggage", "mathematics", "mayonnaise", "meat", "mustard", "news", "progress", "research", "rice", "sand", "software", "understanding", "water")),
		 array("s-singular", array("acropolis", "aegis", "alias", "asbestos", "bathos", "bias", "caddis", "cannabis", "canvas", "chaos", "cosmos", "dais", "digitalis", "epidermis", "ethos", "gas", "glottis", "glottis", "ibis", "lens", "mantis", "marquis", "metropolis", "pathos", "pelvis", "polis", "rhinoceros", "sassafras", "trellis")),
		 array("ex-ices", array("codex", "murex", "silex")),
		 array("ex-ices-classical", array("apex", "cortex", "index", "latex", "pontifex", "simplex", "vertex", "vortex")),
		 array("um-a", array("agendum", "bacterium", "candelabrum", "datum", "desideratum", "erratum", "extremum", "ovum", "stratum")),
		 array("um-a-classical", array("aquarium", "compendium", "consortium", "cranium", "curriculum", "dictum", "emporium", "enconium", "gymnasium", "honorarium", "interregnum", "lustrum", "maximum", "medium", "memorandum", "millenium", "minimum", "momentum", "optimum", "phylum", "quantum", "rostrum", "spectrum", "speculum", "stadium", "trapezium", "ultimatum", "vacuum", "velum")),
		 array("on-a", array("aphelion", "asyndeton", "criterion", "hyperbaton", "noumenon", "organon", "perihelion", "phenomenon", "prolegomenon")),
		 array("a-ae", array("alga", "alumna", "vertebra")),
		 array("a-ae-classical", array("abscissa", "amoeba", "antenna", "aurora", "formula", "hydra", "hyperbola", "lacuna", "medusa", "nebula", "nova", "parabola")),
		 array("en-ina-classical", array("foramen", "lumen", "stamen")),
		 array("a-ata-classical", array("anathema", "bema", "carcinoma", "charisma", "diploma", "dogma", "drama", "edema", "enema", "enigma", "gumma", "lemma", "lymphoma", "magma", "melisma", "miasma", "oedema", "sarcoma", "schema", "soma", "stigma", "stoma", "trauma")),
		 array("is-ides-classical", array("clitoris", "iris")),
		 array("us-i-classical", array("focus", "fungus", "genius", "incubus", "nimbus", "nucleolus", "radius", "stylus", "succubus", "torus", "umbilicus", "uterus")),
		 array("us-us-classical", array("apparatus", "cantus", "coitus", "hiatus", "impetus", "nexus", "plexus", "prospectus", "sinus", "status")),
		 array("o-i-classical", array("alto", "basso", "canto", "contralto", "crescendo", "solo", "soprano", "tempo")),
		 array("-i-classical", array("afreet", "afrit", "efreet")),
		 array("-im-classical", array("cherub", "goy", "seraph")),
		 array("o-os", array("albino", "archipelago", "armadillo", "commando", "ditto", "dynamo", "embryo", "fiasco", "generalissimo", "ghetto", "guano", "inferno", "jumbo", "lingo", "lumbago", "magneto", "manifesto", "medico", "octavo", "photo", "pro", "quarto", "rhino", "stylo")),
		 array("general-generals", array("Adjutant", "Brigadier", "Lieutenant", "Major", "Quartermaster", "adjutant", "brigadier", "lieutenant", "major", "quartermaster"))
	);
	
	static $singular_rules = array(
		'(?i)(.)ae$' => '\\1a',
		'(?i)(.)itis$' => '\\1itis',
		'(?i)(.)eaux$' => '\\1eau',
		'(?i)(quiz)zes$' => '\\1',
		'(matr)ices$' => "\\1ix",
		'(?i)(vert|ind)ices$' => '\\1ex',
		'(?i)^(ox)en' => '\\1',
		'(?i)(alias|status)es$' => '\\1',
		'(?i)([octop|vir])i$' => '\\1us',
		'(?i)(cris|ax|test)es$' => '\\1is',
		'(?i)(shoe)s$' => '\\1',
		'(?i)(o)es$' => '\\1',
		'(?i)(bus)es$' => '\\1',
		'(?i)([m|l])ice$' => '\\1ouse',
		'(?i)(x|ch|ss|sh)es$' => '\\1',
		'(?i)(m)ovies$' => '\\1ovie',
		'(?i)ombies$' => '\\1ombie',
		'(?i)(s)eries$' => '\\1eries',
		'(?i)([^aeiouy]|qu)ies$' => '\\1y',
		"([aeo]l)ves$" => "\\1f",
		"([^d]ea)ves$" => "\\1f",
		"arves$" => "arf",
		"erves$" => "erve",
		"([nlw]i)ves$" => "\\1fe",   
		'(?i)([lr])ves$' => '\\1f',
		"([aeo])ves$" => "\\1ve",
		'(?i)(sive)s$' => '\\1',
		'(?i)(tive)s$' => '\\1',
		'(?i)(hive)s$' => '\\1',
		'(?i)([^f])ves$' => '\\1fe',
		'(?i)(^analy)ses$' => '\\1sis',
		'(?i)((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$' => '\\1\\2sis',
		'(?i)(.)opses$' => '\\1opsis',
		'(?i)(.)yses$' => '\\1ysis',
		'(?i)(h|d|r|o|n|b|cl|p)oses$' => '\\1ose',
		'(?i)(fruct|gluc|galact|lact|ket|malt|rib|sacchar|cellul)ose$' => '\\1ose',
		'(?i)(.)oses$' => '\\1osis',
		'(?i)([ti])a$' => '\\1um',
		'(?i)(n)ews$' => '\\1ews',
		'(?i)s$' => ''
	);
	
	static $singular_irregular = array(
		"men" => "man",
		"people" => "person",
		"children" => "child",
		"sexes" => "sex",
		"moves" => "move",
		"teeth" => "tooth",
		"geese" => "goose",
		"feet" => "foot",
		"zoa" => "zoon",
		"atlantes" => "atlas", 
		"atlases" => "atlas", 
		"beeves" => "beef", 
		"brethren" => "brother", 
		"children" => "child", 
		"corpora" => "corpus", 
		"corpuses" => "corpus", 
		"kine" => "cow", 
		"ephemerides" => "ephemeris", 
		"ganglia" => "ganglion", 
		"genii" => "genie", 
		"genera" => "genus", 
		"graffiti" => "graffito", 
		"helves" => "helve",
		"leaves" => "leaf",
		"loaves" => "loaf", 
		"monies" => "money", 
		"mongooses" => "mongoose", 
		"mythoi" => "mythos", 
		"octopodes" => "octopus", 
		"opera" => "opus", 
		"opuses" => "opus", 
		"oxen" => "ox", 
		"penes" => "penis", 
		"penises" => "penis", 
		"soliloquies" => "soliloquy", 
		"testes" => "testis", 
		"trilbys" => "trilby", 
		"turves" => "turf", 
		"numena" => "numen", 
		"occipita" => "occiput", 
	);
	
	static $singular_uninflected = array("bison", "bream", "breeches", "britches", "carp", "chassis", "clippers", "cod", "contretemps", "corps", "debris", "diabetes", "djinn", "eland", "elk", "flounder", "gallows", "graffiti", "headquarters", "herpes", "high-jinks", "homework", "innings", "jackanapes", "mackerel", "measles", "mews", "mumps", "news", "pincers", "pliers", "proceedings", "rabies", "salmon", "scissors", "series", "shears", "species", "swine", "trout", "tuna", "whiting", "wildebeest");
	static $singular_uncountable = array("advice", "bread", "butter", "cheese", "electricity", "equipment", "fruit", "furniture", "garbage", "gravel", "happiness", "information", "ketchup", "knowledge", "love", "luggage", "mathematics", "mayonnaise", "meat", "mustard", "news", "progress", "research", "rice", "sand", "software", "understanding", "water");
	
	static $singular_ie = array("algerie", "auntie", "beanie", "birdie", "bogie", "bombie", "bookie", "cookie", "cutie", "doggie", "eyrie", "freebie", "goonie", "groupie", "hankie", "hippie", "hoagie", "hottie", "indie", "junkie", "laddie", "laramie", "lingerie", "meanie", "nightie", "oldie", "^pie", "pixie", "quickie", "reverie", "rookie", "softie", "sortie", "stoolie", "sweetie", "techie", "^tie", "toughie", "valkyrie", "veggie", "weenie", "yuppie", "zombie");
	

//	""" Returns the plural of a given word.	
//	For example: child -> children.
//	Handles nouns and adjectives, using classical inflection by default
//	(e.g. where "matrix" pluralizes to "matrices" instead of "matrixes".


	private function plural1($word, $pos, $classical, $plural_prepositions, $plural_rules, $plural_categories)
	{ 
	// Recursion of genitives
	// remove the apostrophe and any trailing -s, 
	// form the plural of the resultant noun, and then append an apostrophe.
	// (dog's -> dogs')
		if ((strlen($word) > 0 && substr($word, -2, 1) == ",") || (strlen($word) > 1 && substr($word, -2, 2) == "'s")) {
			$word = substr($word, 0, -2);
			$owners = self::plural($word, $pos, $classical, $plural_prepositions, $plural_rules, $plural_categories);
			if (substr($owners, -1, 1) == "s") {
				return $owners . "'";
				}
			else
				return $owners . "'s";
		 }	   
	// Recursion of compound words
	// (Postmasters General, mothers-in-law, Roman deities).
		$words1 = str_replace("-", " ", $word);
		$words = explode(" " , $words1);
		if (count($words) > 1) {
			if ((($words[1] == "general") || ($words[1] == "General")) && (in_array($words[0], $plural_categories[19][1]) == false)) {
				$wordk = $word;
				$word = $words[0];
				$results = self::plural($word, $pos, $classical, $plural_prepositions, $plural_rules, $plural_categories);
				return str_replace($words[0], $results, $wordk);
			}
			elseif (in_array($words[1], $plural_prepositions)) {
				 $wordk = $word;
				 $word = $words[0];
				 $results = self::plural($word, $pos, $classical, $plural_prepositions, $plural_rules, $plural_categories);
				 return str_replace($words[0], $results, $wordk);
				 }
			else
				$nowr = count($words) - 1;
				$wordk = $word;
				$word = $words[$nowr];
				$results = self::plural($word, $pos, $classical, $plural_prepositions, $plural_rules, $plural_categories);
				return str_replace($words[$nowr], $results, $wordk);
		}
		
				$results = self::plural($word, $pos, $classical, $plural_prepositions, $plural_rules, $plural_categories);
				return $results;
	}
	 
	 
	private function plural($word, $pos, $classical, $plural_prepositions, $plural_rules, $plural_categories)
	{   
	// Only a very few number of adjectives inflect.
		$n = count($plural_rules);
		if ($pos == "adjective") {
			$n = 0;
		}
		for ($i=0; $i<=$n - 1; $i++)
		{
		  $nn = count($plural_rules[$i]);
			for ($ii=0; $ii<=$nn - 1; $ii++)	
			 {
				//A general rule, or a classic rule in classical mode.
				if (($plural_rules[$i][$ii][2] == "none")) {
					if (($plural_rules[$i][$ii][3] == "false") || (($plural_rules[$i][$ii][3] == "true") && $classical == "true"))
					{
					   if (preg_match("/" . $plural_rules[$i][$ii][0] . "/i", $word)) {
					   return preg_replace("/" . $plural_rules[$i][$ii][0] . "/i", $plural_rules[$i][$ii][1], $word);
					   }
					 }  
				 }
				//A rule relating to a specific category of words	
				if ($plural_rules[$i][$ii][2] != "none") {
					$cate = $plural_rules[$i][$ii][2];
				   if (in_array(strtolower($word), $plural_categories[$cate][1]) && (($plural_rules[$i][$ii][3] != "true") || (($plural_rules[$i][$ii][3] == "true") && $classical == "true")))
					   {
						if (preg_match("/" . $plural_rules[$i][$ii][0] . "/i", $word) ) {
						return preg_replace("/" . $plural_rules[$i][$ii][0] . "/i", $plural_rules[$i][$ii][1], $word);
					   }
					}
				  }
			  }
			}	  
				  	
		return $word;
	}
	
	//	   Returns the singular of a given word.
	private function singular($word, $singular_rules, $singular_uninflected, $singular_uncountable, $singular_ie, $plural_prepositions, $singular_irregular)
	{
		// Recursion of compound words (e.g. mothers-in-law).
		$words1 = str_replace("-", " ", $word);
		$words = explode(" " , $words1);
			if (count($words) > 1) {
			  if (in_array($words[1], $plural_prepositions)) {
				$wordk = $word;
				$word = $words[0];
				$results = self::singular($word, $singular_rules, $singular_uninflected, $singular_uncountable, $singular_ie, $plural_prepositions, $singular_irregular);
				  return str_replace($words[0], $results, $wordk);
			  }
			  else
				$nowr = count($words) - 1;
				$wordk = $word;
				$word = $words[$nowr];
				$results = self::singular($word, $singular_rules, $singular_uninflected, $singular_uncountable, $singular_ie, $plural_prepositions, $singular_irregular);
				return str_replace($words[$nowr], $results, $wordk);
			}	
				
		$lower_cased_word = strtolower($word);
		$len = strlen($lower_cased_word);
		for ($i=0; $i<=count($singular_uninflected) - 1; $i++)
		{
		 $part = substr($singular_uninflected[$i], 0, - $len);
		   if ($part == $lower_cased_word) {
			 return $word;
		   }
		 }	
		for ($i=0; $i<=count($singular_uncountable) - 1; $i++)
		{
		 $part = substr($singular_uncountable[$i], 0, - $len);
		   if ($part == $lower_cased_word) {
			 return $word;
		   }
		 }	  
		for ($i=0; $i<=count($singular_ie) - 1; $i++)
		{
		 $len = strlen($singular_ie[$i]) + 1;
		 $part = substr($lower_cased_word, 0, - $len);
		   if ($part == $singular_ie[$i] . "s") {
			 return $singular_ie[$i];
		   }
		 }
		 
		foreach ($singular_irregular as $pattern => $result)
			{
				$pattern = '/' . $pattern . '$/i';
				if (preg_match( $pattern, $word) ) {
					return preg_replace($pattern, $result, $word);
					}
			}
	
		foreach ($singular_rules as $pattern => $result)
			{
				$pattern = '/' . $pattern . '/i';
				//echo $pattern . "  " . $word;
				//echo "<br>\n";
				if (preg_match( $pattern, $word) ) {
					return preg_replace($pattern, $result, $word);
					}
			}
			
		return $word;
	}

	public function pluralize($word, $nadj = 'noun', $classical = true) {
		$classical = $classical? 'true':'false';
		return self::plural1(strtolower($word), $nadj, $classical, self::$plural_prepositions, self::$plural_rules, self::$plural_categories);
	}

	public function singularize($word) {
		return self::singular(strtolower($word), self::$singular_rules, self::$singular_uninflected, self::$singular_uncountable, self::$singular_ie, self::$plural_prepositions, self::$singular_irregular);
	}
	
}
?>