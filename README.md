# Unifi Controller Monitor

This simple script monitors the unifi controller for new hosts. When a new host is found, it emits an email alerting you that a new host is on the network.

**NOTE:** This script can be easily circumvented by a careful bad agent. If the bad agent spoofs a mac address already associated with your access point, no alert will be emitted. This is only intended for casual alerting of new hosts on the network.

## Installation

### Docker

TODO: Add instructions


### Running Standalone

You should be able to run this standalone, but I have not tested this.
