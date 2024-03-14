## Installation
Installing this package is done using Composer:
```
composer require vercoutere/laravel-mjml
```
## Configuration
### Rendering strategies
There are two available strategies to use for rendering MJML using this package:
- `local`: Uses a local MJML binary.
- `api`: Uses the publicly available [MJML API](https://mjml.io/api).

The strategy can be configured using an environment variable.
```
MJML_STRATEGY=local/api
```
#### Local strategy
To use the local strategy you will need to have both NodeJS and MJML installed on your machine.
It is recommended to install MJML using the available [npm package](https://www.npmjs.com/package/mjml).

If the node installation you want MJML to use is located at any path other than just `node`, you will need to configure it.
```
MJML_NODE_PATH=/path/to/your/node/installation
```
If you're not installing MJML using the npm package, you will also need to configure the path to your MJML installation:
```
MJML_BINARY_PATH=/path/to/your/mjml/installation
```
#### API strategy
When using the API strategy, you will need to configure your application id and secret.
```
MJML_APP_ID=your-app-id
MJML_SECRET_KEY=your-secret
```
## Usage
To use MJML in your mailables, make sure your mails extend the `Vercoutere\LaravelMjml\MjmlMailable` class instead of the default `Illuminate\Mail\Mailable` class.
```php
use Illuminate\Mail\Mailables\Content;
use Vercoutere\LaravelMjml\MjmlMailable;

class MyMail extends MjmlMailable
{
    public function content(): Content
    {
        return new Content(
            view: 'my-template',
        );
    }
}
```
If your blade template file contains valid MJML, it will now automatically be converted.
```html
<mjml>
  <mj-body>
    <mj-section>
      <mj-column>
        <mj-text font-size="20px" color="#F45E43">{{ App::environment() }}</mj-text>
      </mj-column>
    </mj-section>
  </mj-body>
</mjml>
```
