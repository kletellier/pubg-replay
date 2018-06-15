 

## Pubg replay

 Website from displaying PUBG (Playerunknown's Battleground) animated replays on HTML5 canvas.

 This website has been developped on Laravel 5.6, with Jquery, Bootstrap 3 and KonvaJs, SimpleImage.

 You need PHP 7.1 server with GD extension and Redis server

## Installation

- Clone this repo on your server
- Copy .env-example to .env : ```cp .env-example .env```
- Fill PUBG_KEY with your Pubg Api key in .env file, if you doesn't have one, claim it [Pubg API] (https://developer.playbattlegrounds.com/?loc=en) 
- Fill your Redis server info in .env file
- Execute ```composer update --prefer-dist -o``` 
- Execute ``` php artisan key:generate```
- You can test with internal php server : ``` php artisan serve``` and browse http://127.0.0.1:8000
- Or add it in your webserver and point it on public folder

## Screenshots

![Search](http://pubg-replay.kletellier.xyz/images/menu.png) 

![List](http://pubg-replay.kletellier.xyz/images/list.png) 

![Replay](http://pubg-replay.kletellier.xyz/images/replay.png) 


## License

MIT License

Copyright (c) [2018] [Pubg Replay]

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
 
