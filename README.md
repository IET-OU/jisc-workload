# Jisc Student Workload Tool

A web-based tool for recording, visualising and managing student workload on courses,
developed by The Open University with support from [Jisc][] micro-project funding.

[Background reading][blog].

## Requirements

- Linux, Max OS X or Windows
- Apache 2.2+ (`mod_rewrite`)
- PHP 5.4+ (JSON enabled)
    * [Composer][]

## Installation

Before installing the files in the document root of the web server, a few changes
need to be made to a configuration file and a database needs to be created on the
web server which can be accessed by the workload tool.

01. Run `composer setup-config`
01. Open the file `site/config/config.php`
02. Scroll to the bottom of the file
03. Change the `mailer` entry with the correct host, username and password for the mail server to be used
04. If you want to change the default database name and login details, change those in the `db` entry
05. Save the file
06. Copy the entire directory structure and files to the document root of the web server
07. On the webserver, create a database named `jisc_workload`
08. Create a database user with the username `jisc_workload` and password `w0rkl0ad!`
    (You can use a different username and password but you'll need to change these in the config file)
09. Grant the user `jisc_workload` all privileges on database `jisc_workload`
10. Open the file `framework/tables.sql`
11. Scroll to the bottom of the file
12. Change `[Name of your institution]` on line 148 to the name of your institution
13. Change `[First name]`, `[Last name]`, `[Email]`, `[Login]`, and `[Password]` on line 151 to the details of
    the first system administrator (additional administrators can be created once the system is live)
12. Copy the entire file to the clipboard and run it as an SQL query on the newly created database
    (This is easiest using a tool such as phpMyAdmin)

You should now be able to access the tool on the domain name linked to the web server.

## License

* [GNU General Public License 3.0+][gpl]

--

Contributors:  [@djitsz][] (original developer)

---
© 2015 The Open University ([Institute of Educational Technology][iet]).


[blog]: http://analytics.jiscinvolve.org/wp/2016/02/09/guest-post-jisc-ou-student-workload-tool/
[composer]: https://getcomposer.org/ "Dependency Manager for PHP"
[gpl]: https://gnu.org/licenses/gpl.html
[src]: https://github.com/IET-OU/
[jisc]: https://jisc.ac.uk/ "formerly the Joint Information Systems Committee, UK"
[iet]: http://iet.open.ac.uk/
[@djitsz]: https://github.com/djitsz "Jitse van Ameijde"

[End]: //end.
