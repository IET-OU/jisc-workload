[![Build status — Travis-CI][travis-icon]][travis]
[![Code Climate][climate-icon]][climate]

# Jisc Student Workload Tool

A web-based tool for recording, visualising and managing student workload on courses,
developed by The Open University ([IET][]) with support from [Jisc][] micro-project funding.

* [Background reading][blog].

## Requirements

- Linux, Max OS X or Windows
- Apache 2.2+ (`mod_rewrite`)
- PHP 5.4+ (JSON enabled)
    * [Composer][]
- MySQL

## Installation

Before installing the files in the document root of the web server, a few changes
need to be made to a configuration file and a database needs to be created on the
web server which can be accessed by the workload tool.

01. Run `composer install && composer setup-config`
01. Open the file [`site/config/config.php`][config.php]
02. Scroll to the bottom of the file
03. Change the `mailer` entry with the correct host, username and password for the mail server to be used
04. If you want to change the default database name and login details, change those in the `db` entry
05. Save the file
06. Copy the entire directory structure and files to the document root of the web server, or `git clone`
07. On the webserver, create a database named `jisc_workload`
08. Create a database user with the username `jisc_workload` and password `w0rkl0ad!`
    (You can use a different username and password but you'll need to change these in the config file)
09. Grant the user `jisc_workload` all privileges on database `jisc_workload`
10. Open the file [`framework/tables.sql`][tables.sql]
11. Scroll to the bottom of the file
12. Change `[Name of your institution]` on line 148 to the name of your institution
14. Change `[First name]`, `[Last name]`, `[Email]`, `[Login]`, and `[Password]` on line 151 to the details of
    the first system administrator (additional administrators can be created once the system is live)
15. Copy the entire file to the clipboard and run it as an SQL query on the newly created database
    (This is easiest using a tool such as phpMyAdmin)

You should now be able to access the tool on the domain name linked to the web server.

## Acknowledgements

* Contributors:  [@djitsz][] (original developer)
* Bundled libraries:  jQuery, Bootstrap, PHP [SwiftMailer][].
* Funding: [Jisc][]

## License

* [GNU General Public License 3.0+][gpl]


---
© 2015 The Open University ([Institute of Educational Technology][iet]).


[blog]: http://analytics.jiscinvolve.org/wp/2016/02/09/guest-post-jisc-ou-student-workload-tool/
[composer]: https://getcomposer.org/ "Dependency Manager for PHP"
[gpl]: https://gnu.org/licenses/gpl.html
[src]: https://github.com/IET-OU/
[jisc]: https://jisc.ac.uk/ "formerly the Joint Information Systems Committee, UK"
[iet]: http://iet.open.ac.uk/ "Institute of Educational Technology"
[@djitsz]: https://github.com/djitsz "Jitse van Ameijde"
[swiftmailer]: https://packagist.org/packages/swiftmailer/swiftmailer

[config.php]: https://github.com/IET-OU/jisc-workload/blob/master/site/config/config.DIST.php#L205-L217
[tables.sql]: https://github.com/IET-OU/jisc-workload/blob/master/framework/tables.DIST.sql#L161-L165

[travis]: https://travis-ci.org/IET-OU/jisc-workload "Build status — Travis-CI"
[travis-icon]: https://travis-ci.org/IET-OU/jisc-workload.svg
[climate]: https://codeclimate.com/github/IET-OU/jisc-workload "Code Climate"
[climate-icon]: https://codeclimate.com/github/IET-OU/jisc-workload/badges/gpa.svg

[End]: //end.
