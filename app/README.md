### Welcome to the `app` directory!

This is the core of our application, housing the main components that shape its structure and functionality.

Before we dive into the subdirectories, let's take a look at some important files in this directory:

#### Entry.php

`Entry.php` is the bootstrap of the application. It sets up the application kernel, injects the service providers,
applies middleware, initializes the routes, and starts the application. It uses the Config class to get the application
configuration and applies middleware based on the configuration.

#### Route.php

`Route.php` is responsible for defining the routes of the application. It uses the Router class to define routes for
different HTTP methods and paths. It also defines error handlers for different types of exceptions. The routes map to
methods in controller classes which handle the requests.

#### Config.php

`Config.php` is a class that manages the configuration of the application. It uses the Singleton pattern to
ensure that only one instance of the configuration is created. The configuration values are fetched from the environment
variables and stored in the instance properties. This class provides a static method `instance()` to get its instance.

Now, let’s move on to the subdirectories:

- `Controller`: Controllers are the traffic managers of our app. They handle incoming HTTP requests and return the right
  responses. They call on the appropriate services to do the heavy lifting and then deliver the response back to the
  user.

- `Core`: This is where the framework of our application resides. It includes everything from the application's
  container, context, exception handling, to HTTP request and response handling, the application kernel, logging,
  middleware, rendering, and routing. Essentially, it's the central hub for many of the application's most
  important operations.

- `Dto`: This is where all the Data Transfer Objects (DTOs) used in the application live. DTOs are handy objects that
  convert raw requests into PHP objects, helping with user request validation, keeping our code clean, and facilitating
  easy data movement across the app.

- `Exception`: This is where all the custom exceptions for our application are stored. We use these exceptions to handle
  any specific errors that might occur while the application is running.

- `Lib`: This plays a role similar to the `vendor/` directory in Composer. We keep our custom libraries here,
  like API Clients, that can be used anywhere in the application. These libraries are designed to be independent and
  reusable.

- `Middleware`: This is where all the middleware for the application is located. Think of middleware as the bouncer at
  the club. It wraps our HTTP requests and responses with some extra moves, like logging, authentication, and more. It's
  all about keeping things in check.

- `Model`: Models are PHP objects that define resource data, keeping things organized and easy to use. These
  objects come in handy when we're pulling data from databases or external APIs.

- `Repository`: This is where all the repositories for the application are stored. They're like the magic box that knows
  how to communicate with our data sources. They don't care where or how they're used. They just blindly do their jobs.

- `Service`: Services are like the brains of our app, handling all the heavy-duty business logic. They oversee
  transactions and making sure everything in our app responds just the way it should.
