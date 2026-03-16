# Oranguru — Private, peaceful task management

Oranguru is a lightweight task manager that helps you stay focused on today's priorities.

<p><img alt="The Today view in Oranguru" src="./demo.png" width="480"></p>

Oranguru runs in your browser and stores your data in [`localStorage`](https://developer.mozilla.org/en-US/docs/Web/API/Window/localStorage). You can export your data at any time. If your browser supports [`showSaveFilePicker()`](https://developer.mozilla.org/en-US/docs/Web/API/Window/showSaveFilePicker), you can also enable automatic backups.

Oranguru can't sync your data between devices. However, if you [register your main device](https://oranguru.com/help#registering-for-a-personal-link), the oranguru.com server creates a personal link that you can use to send tasks to your main device.

In this README:

  - [Using Oranguru offline](#using-oranguru-offline)
  - [How your personal link works](#how-your-personal-link-works)
  - [Self-hosting Oranguru](#self-hosting-oranguru)
  - [Acknowledgments](#acknowledgments)
  - [License](#license)

## Using Oranguru offline

Oranguru Local is a version of [oranguru.com](https://oranguru.com) that you can run on your computer. With Frogab Local, you can:

  - Use Oranguru offline
  - Automatically back up your data in any browser
  - Send tasks to Oranguru via a terminal

[Install the Linux snap](https://snapcraft.io/oranguru) or [install the Python package](https://github.com/dwilding/oranguru/blob/main/local/README.md#oranguru-local).

## How your personal link works

 1. When you register your device, Oranguru generates a PGP key pair in your browser. Your device then sends the public key to the server. The private key never leaves your device.

    See `register()` in [help.html](app/help.html).

 2. The server generates a user ID and an API key for your device:

      - **User ID** - The public "address" of your device
      - **API key** - A non-public "password" for your device

    See [post-create-user.php](server/public/open/post-create-user.php).

    Your personal link is `https://oranguru.com/send#{id}`, where `{id}` is the user ID.

 3. When you use your personal link to send a task, Oranguru first encrypts the task using the public key from step 1. Oranguru then sends the encrypted task to the server.

    See `encryptAndSend()` in [send.html](app/send.html).

 4. The server queues the encrypted task.

    See [post-add-message.php](server/public/open/post-add-message.php).

 5. Your device periodically checks for encrypted tasks.

    The server requires the API key from step 2. This ensures that other devices cannot check for encrypted tasks. If there are encrypted tasks in the queue, your device downloads the encrypted tasks.

    The server clears the queue as soon as your device has downloaded the encrypted tasks.

    See [post-remove-messages.php](server/public/open/post-remove-messages.php).

 6. Your device decrypts the tasks using the private key from step 1.

    See `verifyUserAndAppendMessages()` in [main.js](app/main.js).

## Self-hosting Oranguru

You'll need an Apache server with PHP and [Composer](https://getcomposer.org/). Apache must have the following modules enabled:

  - mod_mime
  - mod_rewrite
  - mod_headers

To install Oranguru on your own server:

 1. Open a shell on your server, then navigate to a directory that is accessible to PHP scripts but not accessible via the web.

 2. Download [install_oranguru.sh](extra/install_oranguru.sh) and make it executable:

    ```
    wget https://oranguru.com/install_oranguru.sh
    chmod +x install_oranguru.sh
    ```

 3. Run the following command:

    ```
    ./install_oranguru.sh /path/to/public
    ```

    Where */path/to/public* is a directory that is accessible via the web.

To use Oranguru, open your browser, then navigate to the web-accessible directory from step 3.

The first time you register a device, Oranguru creates an SQLite database called *oranguru.db* in the directory from step 1. This database stores device credentials and the queue of encrypted tasks.

Anyone who knows the URL of your installation will be able to register their device. To reject further registration attempts after you've registered your device, set `allow_registration = false` in *oranguru.toml* (in the directory from step 1).

> [!TIP]
> To update your installation, run `./install_oranguru.sh /path/to/public` again. For information about the latest changes, see [merged pull requests](https://github.com/dwilding/oranguru/pulls?q=state%3Amerged+label%3Aserver+sort%3Acreated-desc) or the [feed](https://oranguru.com/changes.xml).

## Acknowledgments

Huge thanks to:

  - Ben Ramsey for [ramsey/uuid](https://uuid.ramsey.dev)
  - [Iconnoir](https://iconoir.com)
  - Kev Quirk for [Simple.css](https://simplecss.org)
  - Lars Kiesow for [python-feedgen](https://feedgen.kiesow.be)
  - [Mackenzie W](https://www.fiverr.com/mackwhyte) for the Oranguru logo!
  - [OpenPGP.js](https://openpgpjs.org)
  - Vano Devium for [devium/toml](https://github.com/vanodevium/toml)

## License

Oranguru is licensed under the MIT License. For details, see [LICENSE](LICENSE).

Oranguru uses OpenPGP.js for PGP encryption. The source code of OpenPGP.js is available at https://github.com/openpgpjs/openpgpjs. OpenPGP.js is licensed under the GNU Lesser General Public License. For details, see [LICENSE_openpgp](LICENSE_openpgp).
