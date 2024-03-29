from machine import Pin, ADC, PWM
from utime import sleep

b100 = {
	"\r":"103",
	"\t":"108",
	"\n":"113",
	" ":"118",
	"!":"123",
	"\"":"128",
	"#":"133",
	"$":"138",
	"%":"143",
	"&":"148",
	"'":"153",
	"(":"158",
	")":"163",
	"*":"168",
	"+":"173",
	",":"178",
	"-":"183",
	".":"188",
	"/":"193",
	"0":"198",
	"1":"203",
	"2":"208",
	"3":"213",
	"4":"218",
	"5":"223",
	"6":"228",
	"7":"233",
	"8":"238",
	"9":"243",
	":":"248",
	";":"253",
	"<":"258",
	"=":"263",
	">":"268",
	"?":"273",
	"@":"278",
	"A":"283",
	"B":"288",
	"C":"293",
	"D":"298",
	"E":"303",
	"F":"308",
	"G":"313",
	"H":"318",
	"I":"323",
	"J":"328",
	"K":"333",
	"L":"338",
	"M":"343",
	"N":"348",
	"O":"353",
	"P":"358",
	"Q":"363",
	"R":"368",
	"S":"373",
	"T":"378",
	"U":"383",
	"V":"388",
	"W":"393",
	"X":"398",
	"Y":"403",
	"Z":"408",
	"[":"413",
	"\\":"418",
	"]":"423",
	"^":"428",
	"_":"433",
	"`":"438",
	"a":"443",
	"b":"448",
	"c":"453",
	"d":"458",
	"e":"463",
	"f":"468",
	"g":"473",
	"h":"478",
	"i":"483",
	"j":"488",
	"k":"493",
	"l":"498",
	"m":"503",
	"n":"508",
	"o":"513",
	"p":"518",
	"q":"523",
	"r":"528",
	"s":"533",
	"t":"538",
	"u":"543",
	"v":"548",
	"w":"553",
	"x":"558",
	"y":"563",
	"z":"568",
	"{":"573",
	"|":"578",
	"}":"583",
	"~":"588",
	"’":"593"
}

adc = ADC(Pin(0))
pwm = PWM(Pin(1))
pwm.freq(60)


countdata = len(data)
counter = 0
while counter < countdata:
	procces = data[counter]
	try:
		strap = b100[procces]
		pwm.duty_u16(strap)
		#print(strap)
		counter += 1
	except:
		pass
	
	adc.read()
