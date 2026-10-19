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

Aryia mentioned $50 from SBC for the website in an October 2 email. That was an earlier budget figure, not confirmation of a recurring hosting allocation. On October 18 Shriya left the server choice to Yinka and asked that the cost details go to Aryia for the board's budget requests. The $50 from WSA will reimburse the domain. The proposed two-server base cost exceeds it even before backup and tax. Ask who will pay the provider directly, who owns the account, and what annual amount the board can approve.

Before choosing a plan, settle these points:

1. **Whole-site move:** Plan to move the WordPress database, uploads, themes, plugins, configuration, public domain, and editorial access together. Check article URLs, search, forms, and outbound mail on the test server. Before changing DNS, set a content-freeze window, take a fresh backup, and record how to return traffic to the current server if the new one fails.
2. **Capacity and price:** Check current WordPress memory, disk, database, and traffic use against the 2 GB / 60 GB plan. Confirm the final cart price, billing schedule, renewal and cancellation terms, tax, and backup charges before committing to a one-year term.
3. **Backups:** Name the person responsible for daily database and uploaded-media backups, retention, alerts, and a restore check. Record where a copy lives away from the production server. Decide whether to use IONOS Cloud Backup, another service, or both.
4. **Updates:** Name who applies operating-system, WordPress core, plugin, and theme updates. Test changes on the test server, then schedule production updates and keep a rollback path.
5. **Handoff:** Put the provider account, domain/DNS access, backup instructions, and recovery contact under an agreed team owner. Don't make the journal depend on one student's personal account.

**Decision needed:** Approve two servers and an annual operating budget, choose a smaller starting arrangement, or keep the current host for now. Do not order or migrate until the board has chosen and the recovery owner is clear.

## Ongoing events calendar

Aryia asked for a calendar that stays current by the next edition. Shriya confirmed on October 18 that the next edition publishes November 19, with finished drafts and graphics in the Drive by November 14. The October release removed the public Calendar and its placeholder events. The repo already has a WordPress `wessci_event` type with title, body, image, start/end time, location, and external URL fields, plus a calendar template. That is a useful starting point after the editors agree on scope; it is not a populated calendar.

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

A test message from a Wesleyan address arrived in Conversations, and the From menu offered the group address. Every email the site sends sets a Reply-To on the group, so replies reach the group instead of the inbox of the account that sends through SMTP.

Plus addresses work: mail to `wesscijo+test@` arrived in the group with the plus address still showing. Posting stays limited to the whole organization, so senders outside Wesleyan get a bounce. Every published address is marked "from your Wesleyan account". Shared labels match the tags but are applied by hand. Google Groups treats some suffixes as commands rather than topics. Mail to `wesscijo+join@` became a request to join the group, so the join address is `+getinvolved`. Avoid `join`, `subscribe`, `unsubscribe`, `leave`, `owner`, and `subconfirm` when adding tags.

| Address | Where it appears |
| --- | --- |
| `wesscijo+submissions@` | Submit page body (WordPress page content) |
| `wesscijo+corrections@` | Below Sources on every article, with the article title in the subject |
| `wesscijo+getinvolved@` | About page, Team tab |
| `wesscijo+web@` | Footer ("Report a site problem"), 404 page, Reply-To on welcome and password-reset emails |
| `wesscijo+authors@` | Reply-To on the publication notice |
| `wesscijo@` | Footer ("Contact"), Reply-To on editorial-team emails |

Google Groups has no profile photo, so mail shows the letter "W". Groups created by students may need a `-glist` suffix.

**Mailing lists:** On October 19 the team chose one address with plus tags and shared labels instead of more Google Groups. Tags sort incoming mail, but they can't send to the leads, the editors, or one section. Sending to those groups would need a Google Contacts label or a roster sheet in the shared drive. An interested-students list would still need a sign-up form or its own group.

**Submit page:** The `+submissions` line was added to the live Submit page (post 22) on October 19, after the guidelines PDF button and before the Disclaimer. WordPress keeps the earlier version as a revision.

**Shared drive:** Yinka is a Contributor on The Wesleyan Science Journal shared drive, and Contributors can't add members. Aryia is a Manager. The October 19 update asks her to add `wesscijo@` as Content manager. The drive's 35 members were added one by one, so linking the group changes nobody's access today. It means people added to the group later get the drive automatically.

**Still open:** Aryia adds the group to the shared drive. Check Pending members for requests that came through the old `+join` link. Decide whether the team needs a way to send to leads and sections.
