# IT490
Super basic skeleton of the project.

Most of this is pseudo-code so probably will not work 100% out of the box.
This is by far not the best practice, it's assuming the reader/view is fairly new to PHP so it tries to 
separate things out into small pieces for easier project progression and easier dividing work amongst team members.
The goal is to separate each "feature" into a separate file so it can be worked on separately, then merged into a primary file via include/require.
Then that primary file be the only thing that needs to be used/inlcuded elsewhere to have access to all of the developed functionality.
There's a common lib folder that's used for the core MQ stuff.


Each VM could/should have their own folder (app, db, api).

In app, the root directory has our view files, a very simple login.php has been included.
We have an MQPublish.inc.php that combines all of our MQ function calls from inside the MQFunctions folder.
This is so we can easily divide up work amongst the team.
Any new MQ calls follow the template of the functions in MQFunctions folder.
Then you include/require the files in MQPublish.inc.php.

Then any of your "view" related files will just need to include/require MQPublish.inc.php to have access to any of the MQ calls.


In DB a similar approach is followed.
We have our consumer where we include functions that wrap our DB calls in DBFunctions folder.
Each file in that folder is a separate DB call. This is so the work can be divided more easily.

As functions are added to the folder they should be included/required in the Consumer.php so they can be used in the request handler.

The DB has a reusable dbconnection that's called in each DBFunction, this is so db connections are shared so you don't run out of connection resources.
You must update the DB credentials here. They should be pulled from environment variables or a private config file. Do not commit this to your repo.

Similar practice as app/db should be followed for api.
API will have a consumer, and instead of a DBFunctions folder you can call it APIRequests.
Then each separate file will be a separate API call.
Then you include/require this in the API's consumer file.
