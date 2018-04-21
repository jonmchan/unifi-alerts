# Unifi Controller Monitor

This simple script monitors the unifi controller for new hosts. When a new host is found, it emits an email alerting you that a new host is on the network.

```
To: jonmchan@myhost.com
From: unifi-monitor@mynetwork.com
Subject: Never Seen 14:10:9f:xx:xx:xx - (jchanmbp) connecting to JJ

Full Info: {
    "1x_identity": "jonathan",
    "_id": "abcdefghijklmnopqrstuvwxyz",
    "_is_guest_by_uap": false,
    "_last_seen_by_uap": 1524349895,
    "_uptime_by_uap": 2473,
    "ap_mac": "f0:9f:c2:xx:xx:xx",
    "assoc_time": 1524347422,
    "authorized": true,
    "bssid": "02:9f:c2:xx:xx:xx",
    "bytes-r": 3730,
    "ccq": 333,
    "channel": 153,
    "essid": "MyNet",
    "first_seen": 1523998057,
    "hostname": "jchanmbp",
    "idletime": 0,
    "ip": "192.168.0.22",
    "is_11r": false,
    "is_guest": false,
    "is_wired": false,
    "last_seen": 1524349895,
    "latest_assoc_time": 1524347423,
    "mac": "14:10:9f:xx:x:xx",
    "noise": -105,
    "oui": "Apple",
    "powersave_enabled": false,
    "qos_policy_applied": true,
    "radio": "na",
    "radio_proto": "na",
    "rssi": 43,
    "rx_bytes": 6713081,
    "rx_bytes-r": 1768,
    "rx_packets": 40391,
    "rx_rate": 300000,
    "signal": -62,
    "site_id": "5ad63ccee4babababa",
    "tx_bytes": 17429567,
    "tx_bytes-r": 1962,
    "tx_packets": 37897,
    "tx_power": 44,
    "tx_rate": 243000,
    "uptime": 2473,
    "user_id": "abcdefghijklmnopqrstuvwxyz",
    "vlan": 0
}
```

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

### Running Standalone

You should be able to run this standalone, but I have not tested this.
