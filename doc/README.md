# Introduction #

phpIP is a web tool developed by [Omnipat](http://www.omnipat.fr/?lang=en) for managing an IP rights portfolio, especially patents. It is intended to satisfy most needs of an IP law firm. The tool was designed to be flexible and simple to use. It is based on an Apache-MySQL-PHP framework.


There are many IP rights management tools out there. They are all proprietary and pretty expensive. It is not the cost, in fact, that led us to designing our own system, because the design resources we spent could equate to the cost of a couple of years license and maintenance fees of existing systems. We found that existing systems are overkill for our needs, because they are designed to satisfy the needs of a majority – hence they have more features than what each individual user needs, so they are very complex to use, yet not all specific needs of the individual user are satisfied. So the user needs to adapt to the system, whereas it should be the other way round.

Since we are patent attorneys and don't have resources for selling and maintaining our software, yet would like others to benefit from it, and hopefully contribute, we decided to open source it. This is an important step in reaching the goal of creating a tool adapted to the user's specific needs. We also designed phpIP to be extremely flexible, so that, hopefully, most users will be able to configure it (and not redesign it) to fit their needs.

# Basic structure of phpIP #

## “Micro-kernel” structure ##

The database is articulated around a central table called _“matter”_ (such as patents, trademarks or designs), that contains minimal information for each matter (essentially a category, a case reference, a country, and an expire date). Information that will be the substance of a matter is freely linked to each matter, in any number, from additional tables: _actors, events, tasks,_ and _classifiers_. We found that anything useful in a matter fits in one of these four tables.

**Actors** are any person or company involved in a matter (applicants, inventors, client, agents...). Each actor can be linked to a matter with any of several roles, and a same actor can be linked to a matter with multiple roles, for instance an inventor can also be an applicant. A client (company) can also be an applicant. All variations are possible.

The roles are contained in the _actor\_role table_.

Actors are linked to matters through a link table called _matter\_actor\_lnk_. This link table contains additional information about the specific link, such as the actor's role, his reference (for clients and agents), his company, a date, his order of display among other actors with the same role...

**Events** are related to the history of the matter and show how the matter is progressing. They basically have a name and a date. They can be a link to another matter if that other matter is involved in the progress of the current matter (see more details below).

**Tasks** are triggered by specific events. They have a name, a deadline, a “done” flag, and an optional “done date”. For instance, in a patent, an event tracking an “examiner action” issued by a patent office will trigger a “Response” task having a deadline three months after the examiner action date.

Tasks can be added manually to events. Some tasks are created automatically based on information in a _task\_rules table_, when corresponding trigger events are entered for the matter.

**Classifiers** are other pieces of useful information that do not correspond to the above, such as keywords, business units, patent classification... and also links to related matters, for instance patents around a same invention. They have a type and a value, or a link to another matter. The value can be free-text stored in the _classifier_ table itself (a value that is specific to the matter and not reused in other matters), or an index in a _classifier\_value_ table containing values that can be used multiple times (such as a list of products or business units).

## No duplication of information ##

One key feature of phpIP is that any piece of information, even if it is used in multiple matters, say patents, is stored in one place only. We use two mechanisms for that:

**Linked events**

For instance, the priority date of a second patent is the filing date of a first patent. In traditional systems, this date is copied into a record related to the second patent. What if the filing date of the first patent is changed? Well, the system or user needs to make sure the date is updated in the second patent. In phpIP, the priority date of the second patent is an event linked to the filing event of the first patent. If the filing date of the first patent is changed, that change is automatically reflected in the priority event of the second patent, and tasks that rely on this date are updated.

**Containers for shared information**

In phpIP, the first matter in a series of related matters, for instance the first filed patent in a family, is a container for information common to all patents in the family, for instance client, inventors and applicants. Classifiers are also shared. Each subsequent created matter in the family shares this information from the container without duplication. If the information needs to be changed, it is changed in one place for all family members. The user may configure what type of information is shared by default and he has the option to change that as he adds the information.

These features make queries for displaying all data related to a matter more complex, but this is all handled by the user interface, so it is transparent to the end user. You will need to be more careful if you intend to write advanced queries but we have provided some view tables that list all the data related to each matter (and hence make the linked event and container techniques transparent).

[Jean J. de Jong](http://www.omnipat.fr/management-team/jean-jacques-de-jong/?lang=en)
