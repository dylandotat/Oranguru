# Oranguru Local

Oranguru Local is a version of the [Oranguru](https://oranguru.com) task manager that runs on localhost. This package provides:

  - The Oranguru Local server
  - A Python interface for configuring, starting, and interacting with the server
  - A command `oranguru`, which wraps the Python interface, for managing the server and sending tasks to Oranguru

For details, see the [Oranguru Local docs](https://github.com/dwilding/oranguru/blob/main/local/README.md#oranguru-local).

This package also provides a command `serve-oranguru /path/to/config.json`, which enables other service managers to run the server as a daemon. 

## License

Oranguru Local is licensed under the MIT License.

Oranguru Local uses OpenPGP.js for PGP encryption. The source code of OpenPGP.js is available at https://github.com/openpgpjs/openpgpjs. OpenPGP.js is licensed under the GNU Lesser General Public License.
