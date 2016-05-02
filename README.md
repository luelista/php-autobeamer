# Show multiple websites on a beamer / display with auto switching

The configuration works with simple text files. The working directory is checked for a file called "config.IP" where IP is the connecting client's IP address. If no such file is found, "config.default" is used.

These files should have the following format:

    #headername:headervalue
    #headername2:headervalue2

    timeout;pageurl
    timeout2;pageurl2
    timeout3;pageurl3

Example:

    #title:The Main Info Beamer

    30;http://mysite.example.com/beamerview1
    30;http://mysite.example.com/beamerview2
    10;http://somewhere.example.net/unimportantview

You should also create an empty file called "checkfile" in the same directory.

This file's timestamp is checked every 15 seconds by the client to see if they need to refresh the page.

If you ever need to modify the config while the display computer is already running, you can simple `touch checkfile` and it will reload.



