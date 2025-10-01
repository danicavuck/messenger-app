## Overview of the Recruitment Exercise

This is a dummy project, which is used to demonstrate knowledge of PHP with Symfony and Angular as well as development in general. It serves as an example with some bad practices included.

### Technologies:

- Backend: Symfony
- Frontend: Angular
- API: REST with an openapi.yaml file

**Duration: 5-8 hours**

## Exercise Structure

### Repository Structure:

`/backend` - Should contain all backend-related files.

`/frontend` - Contains all frontend-related files.

`/docs` - Contains the openapi.yaml file and any additional documentation.

#### Backend (/backend):

Build here your Symfony backend service here.

#### Frontend (/frontend):

app.component.ts - Main application file for Angular.

##### API Definition (/docs):

openapi.yaml

## Tasks:

### Backend:
- [ ] Implement the backend architecture from scratch, which will support the Angular application's API calls.
- [ ] Implement error handling.
- [ ] Implement the plugin system for extensibility (Chatbot).
- [ ] Add authentication for message sending.
### Frontend:
- [ ] Optimize data bindings and state management.
- [ ] Improve the user interface responsiveness.
- [ ] Implement a feature to display message status (sent, received).
- [ ] Add seamless communication with the backend application.
- [ ] Create a login form to allow users to log in and send messages.
### API:
- [ ] Review and if necessary correct RESTful API practices.
- [ ] Ensure best practices in the API definition.

## General instructions

- Make sure to follow best practices.
- Pay attention to the code quality as well as software architecture. We value **maintainability** and readability.
- We recommend documenting your changes and the reasoning behind them.
- Git history is important. Make sure to commit your changes as you progress.
- Feel free to ask questions if you have any doubts.
- We are looking for a clean, well-structured solution that demonstrates your understanding of the technologies used.
- The task is only about seeing your skills, nothing more. It is therefore not to be expected that you will work full-time on these 7 days.

## Deliverables

- [ ] send in files with your comments by (one of)
    - Inline-Code-Comments and send us the files
    - drop the files anywhere and send us the link
    - upload the code to your own Repository (Avoid forking the repository and creating a PR, as this would make your solution visible to others)
- [ ] A brief report summarizing the changes you made, why, and **any additional recommendations if they had more time**.
- [ ] Approximate indication of how many hours you worked for this

## Run instructions

- Backend: add your own instructions how to run it
- Frontend: `cd frontend && npm install && npm run serve`
- API: `openapi.yaml` file contains the API definition.
- Access the frontend at `http://localhost:4200`.
- Note: The project is set up to run on localhost by default.
