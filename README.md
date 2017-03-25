# Apple-AppStore-Receipt-PHP
Simple Non-overkill PHP class to verify if purchase is valid and to extract data for further validation.

## Usage
Add
<pre>require_once('apple.php');</pre>
To your project. Then call <b>getReceiptData()</b> with b64 encoded license received from iOS app, 1 for sandbox and 0 for production.
