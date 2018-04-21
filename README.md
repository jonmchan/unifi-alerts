# Unifi Controller Monitor

This simple script monitors the unifi controller for new hosts. When a new host is found, it emits an email alerting you that a new host is on the network.

**NOTE:** This script can be easily circumvented by a careful bad agent. If the bad agent spoofs a mac address already associated with your access point, no alert will be emitted. This is only intended for casual alerting of new hosts on the network.

## Usage

### Docker

Run this easily as a docker container. It runs as a daemon and checks for new hosts every ~15 seconds.

```
$ docker run --name unifi-alerts \
    -v <path to data>:/var/www/data `# only necessary if you want the script to remember previously found hosts` \ 
    -e UNIFI_URL=https://unifi.yours.local:8443 \
    -e UNIFI_USER=unifi_alerts \
    -e UNIFI_PASS=unifi_alerts \
    -e SITE_ID=default \ 
    -e CONTROLLER_VERSION=5.6.36 \
    -e SMTP_HOST=localhost \
    -e SMTP_PORT=25 \
    -e SMTP_USER=user `# remove this if your smtp has no auth` \
    -e SMTP_PASS=pass `# remove this if your smtp has no auth` \
    -e SMTP_FROM=unifi_alerts@unifi.yours.local \
    -e SMTP_TO=you@youraddress.com \
    -d jonmchan/unifi-alerts 
```

You should see the following output in the logs:

```
$ docker logs unifi-alerts

Polling unifi controller for hosts...
New host found - a0:99:9b:xx:xx:xx - (jchan-mbp15) - sending email!
New host found - cc:20:e8:xx:xx:xx - (Jonathan-iPhone) - sending email!
New host found - 24:ab:81:xx:xx:xx - (JChan-Desktop) - sending email!
Polling unifi controller for hosts...
Polling unifi controller for hosts...
Polling unifi controller for hosts...
Polling unifi controller for hosts...
Polling unifi controller for hosts...
```

And you should receive emails like

### Running Standalone

You should be able to run this standalone, but I have not tested this.
