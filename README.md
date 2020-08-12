# Laravel Envoy deployment example for CraftCMS 3.x
This is a fairly simple example for setting up CI auto deployments using Laravel Envoy, including an example gitlab ci config file

## Pre-requisites
- Make sure you've generated a public/private key pair on the server, without a passphrase (`ssh-keygen`)
- Add your public key (`id_rsa.pub`) to gitlab on the required repo (https://gitlab.com/user/repo/-/settings/repository)
- As per your gitlab ci config, add your private key (`id_rsa`) for each of the servers you want to deploy to as a variable (https://gitlab.com/bluemantis/cars2.co.uk/-/settings/ci_cd) - so in this example you'd add STAGING_SSH_PRIVATE_KEY and PRODUCTION_SSH_PRIVATE_KEY

## Setup
- Copy the files in this repo to the root of your craft project
- Update the Envoy.config.php with real values, especially the repository and the a server array for each environment you want to autodeploy to
- Push to one of your branches
- Voila