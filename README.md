# Simple cryptocurrency portfolio in PHP
Create your personal cryptocurrency portfolio

### Requirements
- Webserver that can serve PHP
- lib/data needs to be writable by the webserver to cache images and to write portfolio.json

### Features
- Create your personal cryptocurrency portfolio
- API integration for Kraken, BitTrex and Poloniex
- Add coins manually
- Prices will be calculated in EUR
- Realtime graphs

### Usage
- Clone or copy the the structure into a webfolder
- Modify `api.ini.php` enter your API keys and/or your total investment (for P/L calculation)
- Make lib/data writable
- Open your browser and navigate to the url: to the m00n!



Uses d3.js, c3.js and cryptocompare.com for the API.

![example](lib/data/example_screen_dark.jpg)






