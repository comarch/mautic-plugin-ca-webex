Mautic CaWebex Plugin
=======================
Cisco Webex integration plugin for Mautic by Comarch

### Plugin install
1. Upload plugin to Mautic plugins directory
2. Rename plugin directory to "CaWebexBundle"
3. Rebuild cache `php bin/console cache:clear`
4. Log in to the Mautic as an administrator
5. Click the gear icon in the top right corner of the dashboard, and select the “Plugins” menu item.
6. Click the “Install/Upgrade Plugins” button.
7. Enable Webex plugin and authorize app

### Features
- Form action: Webex Invite
- Monitoring of contacts' participation in meetings
- Add a Tag to contacts invited for the meeting: webex-{ID}-invited
- Add a Tag to contacts registered for the meeting: webex-{ID}-registered
- Add a tag to contacts who attended a meeting: webex-{ID}-attended

### Monitoring configuration
Execute monitoring command manually or add it to the crontab:
```
bin/console mautic:webex:monitoring
```

#### Command parameters
```
 -i, --id                  The id of a specific meeting to process
      --meeting-type       Set meeting type [default: "meetingSeries"]
      --meeting-state      Set meeting state [default: "expired"]
      --from               Set start meeting date/time in UTC timezone. [default: -1 day]
      --to                 Set end meeting date/time in UTC timezone. [default: current datetime]
      --create-contacts]   Create a new contact if a meeting participant does not exist. [default: false]
```