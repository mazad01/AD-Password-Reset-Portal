# AD Password Reset Portal
Active Directory Password Reset Portal with Duo Authentication

What is this?
----
This is a simple Active Directory password reset portal with duo authentication that 
utilizes the combo of Duo Security(Apache Webserver) + PWM password reset module(Apache Tomcat).

Set-Up
----
The PWM module should be set up first at `localhost:8080/pwm` through a basic GUI interface. Once all the LDAP connectivity in Tomcat is valid,
move on to edit the `config.php` in the root of the apache webserver. The variables should suit your environment
and you should have connectivity from both the Apache webserver/tomcat. Finally, on the Duo Admin Dashboard,
create a new integration for a web application and fill in the `AKEY|IKEY|SKEY|HOST` variables on the `index.php`
file accordingly. Finally, start the webserver/tomcat and both should be running successfully. 

References
----
* https://www.duosecurity.com/
* https://code.google.com/p/pwm/
