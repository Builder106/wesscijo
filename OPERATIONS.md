# WesSciJo operations decisions

Updated October 19, 2026, these notes track decisions about hosting, the
events calendar, and the shared journal address. The editors have approved
the shared address setup. They have not approved a server purchase or site
move, or a calendar launch.

## Hosting decision sheet

WordPress handles the entire journal site: public pages, articles, and the editorial system. The proposal sent to the editors is one IONOS VPS for that live site and one for testing. [IONOS currently lists VPS S+](https://www.ionos.com/servers/vps) at $2 per server per month for three months, then $6 per month, with a one-year term. Each server has 1 vCore, 2 GB RAM, and 60 GB NVMe storage. Those are advertised terms, not a checkout quote or a capacity assessment.

| Servers | Months 1-3 | Months 4-12 | First-year server total | Later annual server total if $6/month continues |
| --- | ---: | ---: | ---: | ---: |
| One | $2/month | $6/month | $60 | $72 |
| Two | $4/month | $12/month | $120 | $144 |

These totals exclude tax, backups, and any other add-ons. IONOS lists [Cloud Backup as optional](https://www.ionos.com/servers/vps), starting at $0.065 per GB per month. Its [backup documentation](https://www.ionos.com/help/server-cloud-infrastructure/cloud-backup/overview-backup-package/) says a backup package, agent, schedule, and retention policy must be configured. Extra storage can add charges. IONOS also advertises server images for rollback, but those alone do not establish a scheduled, tested recovery plan.

Aryia mentioned $50 from SBC for the website in an October 2 email. That was an earlier budget figure, not confirmation of a recurring hosting allocation. The proposed two-server base cost exceeds it even before backup and tax. Ask who will pay the provider directly, who owns the account, and what annual amount the board can approve.

Before choosing a plan, settle these points:

1. **Whole-site move:** Plan to move the WordPress database, uploads, themes, plugins, configuration, public domain, and editorial access together. Check article URLs, search, forms, and outbound mail on the test server. Before changing DNS, set a content-freeze window, take a fresh backup, and record how to return traffic to the current server if the new one fails.
2. **Capacity and price:** Check current WordPress memory, disk, database, and traffic use against the 2 GB / 60 GB plan. Confirm the final cart price, billing schedule, renewal and cancellation terms, tax, and backup charges before committing to a one-year term.
3. **Backups:** Name the person responsible for daily database and uploaded-media backups, retention, alerts, and a restore check. Record where a copy lives away from the production server. Decide whether to use IONOS Cloud Backup, another service, or both.
4. **Updates:** Name who applies operating-system, WordPress core, plugin, and theme updates. Test changes on the test server, then schedule production updates and keep a rollback path.
5. **Handoff:** Put the provider account, domain/DNS access, backup instructions, and recovery contact under an agreed team owner. Don't make the journal depend on one student's personal account.

**Decision needed:** Approve two servers and an annual operating budget, choose a smaller starting arrangement, or keep the current host for now. Do not order or migrate until the board has chosen and the recovery owner is clear.

## Ongoing events calendar

Aryia asked for a calendar that stays current by the next edition. Yinka asked whether November 19 is still the target; the date is awaiting confirmation. The October release removed the public Calendar and its placeholder events. The repo already has a WordPress `wessci_event` type with title, body, image, start/end time, location, and external URL fields, plus a calendar template. That is a useful starting point after the editors agree on scope; it is not a populated calendar.

Proposed first version: list verified, upcoming Wesleyan science events that a journal reader could attend. The editors should choose the departments and student groups to watch, whether off-campus or online events qualify, and whether the calendar includes journal deadlines. Start with a short source list rather than promising every science event on campus.

| Step | Proposed owner | Rule |
| --- | --- | --- |
| Collect | Designated calendar editor | Take suggestions from an organizer's official page or message; record the source link. |
| Check | Calendar editor | Confirm title, organizer, local date and time, location or meeting link, and whether the event is open to the intended audience. |
| Approve | An editor-in-chief or delegated editor | Check relevance and wording before publication. |
| Publish | Web editor | Enter the approved event in WordPress; link to the organizer for current details. |
| Maintain | Calendar editor | Review upcoming entries weekly and after organizer changes; correct or remove stale entries. |

For each event, keep the source URL and last-checked date in the working list even if the public card shows only the core details. If the organizer has not confirmed the time or access rules, keep the event unpublished. Use the WordPress site's timezone when entering dates. Do not restore the Calendar navigation link until there are real events and a named editor to maintain them.

**Meeting decisions:** Which sources and event types count? Who is the calendar editor and backup? Who approves entries? How quickly should changes appear? Is November 19 the actual target for the next edition and calendar launch?

## Shared address and group roles

ITS created `wesscijo@wesleyan.edu` as a Google Group. Shriya asked on October 18 that the three editors-in-chief be owners, and the group was set up on October 19, 2026:

| Person | Role |
| --- | --- |
| Yinka Vaughan | Owner |
| Aryia Banihashem-Ahmad | Owner |
| Shriya Sakalkale | Owner |
| Elena Mente | Owner |
| Giancarlo Fedolfi | Member (web support) |

Settings: Collaborative Inbox is on, so conversations can be assigned and marked complete. Only invited users can join. Group members can read conversations, and anyone at Wesleyan can post and attach files. Owners and managers can reply as the group by choosing `wesscijo@wesleyan.edu` in the From menu on the Groups website. The default sender is still the author's own address. There is no moderation, and suspected spam is held for moderators. Member addresses are visible only to members. Outside senders cannot post. If the address is published for people outside Wesleyan, that setting needs another look.

A test message from a Wesleyan address arrived in Conversations, and the From menu offered the group address. Every email the site sends now sets `Reply-To: wesscijo@wesleyan.edu`, so replies reach the group instead of the inbox of the account that sends through SMTP.

Google Groups has no profile photo, so mail shows the letter "W". Groups created by students may need a `-glist` suffix. ITS may need to create the leads, editors, section, and interested-students lists that Shriya asked for.

**Still open:** Link the journal's shared drive to the group as Content manager, and create or request the other lists.
