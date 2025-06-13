# video-interaction-tracker

![image](https://github.com/user-attachments/assets/281f1b56-8694-4480-a881-c87deb7cf8e4)

A **User** accesses a video resource on the **WebApp**. If they aren't logged in, they'll be prompted to submit a **login form**. Once successfully logged in, the **WebApp** starts a **session** and stores related data, like a session ID.

After authenticating, the **User** can access the **Video Page**. The **WebApp** then retrieves and displays **user-specific data**, including their user ID and username.

At the same time, the **Vimeo Player** is initialised to show the video. All user interactions (e.g., **play**, **pause**, **video completion**) are logged in the **Analytics Database** for later analysis.

Finally, if the **User** logs out, the **WebApp** ends the **session**, clearing user data and sending them back to the **Login Page**, where they can log in again if needed.
