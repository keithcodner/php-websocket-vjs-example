# php-websocket-vjs-example
This is a bare bones example of how Vanilla Javascript + PHP works with websockets

#Tired of using libaries for websockets?
Well this example shows you bare bones how a php web sock server works, and how the client interacts with it. I am quiet tired of using libraries, and thinking of this as magic, I want to KNOW how it works.

#Steps to get this running:
1. Download XAMPP or equivalent
2. Go to php.ini set these two values,  ensure they are enabled:
   - TO: max_execution_time=0 FROM: max_execution_time=120
   - TO: extension=sockets FROM: ;extension=sockets
3. Start or restart your web browser
4. Start mysql server and use any client to paste SQL code from db.sql file. It will create the database and schema
5. Go to localhost or 127.0.0.1, in one tab and go to server.php, it will continuously circle leave this tab running
6. In a new tab go to  localhost or 127.0.0.1, and go to index1.php
7. In a new tab or window side by side, go to localhost or 127.0.0.1, and go to index2.php
8. Click the button on index1.php, see that it updates on index2.php

   
