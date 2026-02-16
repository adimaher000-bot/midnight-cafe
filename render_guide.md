# Fast Deployment Guide: Render + TiDB

Since InfinityFree takes too long for DNS, we will use **Render**. It gives you a working URL (like `yourapp.onrender.com`) immediately after deployment.

**The Catch:** Render doesn't give a free database. So we will get a free "Serverless MySQL" from **TiDB Cloud** and connect them.

---

## Part 1: The Database (TiDB Cloud)
1.  Go to **[TiDB Cloud](https://tidbcloud.com/)** and Sign Up (Free).
2.  Click **"Create Cluster"**.
    *   Plan: **Serverless** (Free forever).
    *   Region: Choose one closest to you.
    *   Click **Create**.
3.  **Get Password**:
    *   It will show you a password. **COPY IT**.
4.  **Get Connection Info**:
    *   Click **"Connect"**.
    *   Find the **Host** (e.g., `gateway01.us-west-2.prod.aws.tidbcloud.com`).
    *   Find the **Port** (Usually `4000`, not 3306).
    *   Find the **User** (e.g., `2.root`).
5.  **Import Tables**:
    *   In the TiDB dashboard, click **"Chat2Query"** (or "SQL Editor").
    *   Paste the content of your `sql_fixed/init_wamp.sql` file.
    *   Run it to create the tables.

---

## Part 2: The App (Render)
1.  Go to **[Render.com](https://render.com/)** and Sign Up (GitHub login is best).
2.  Click **"New +"** -> **"Web Service"**.
3.  Select your Repository: `midnight-cafe`.
4.  **Settings**:
    *   **Name**: `midnight-cafe` (or unique name)
    *   **Runtime**: `Docker` (Important!)
    *   **Region**: Closest to you.
    *   **Free Instance**: Yes.
5.  **Environment Variables** (The Magic Step):
    *   Click **"Advanced"** or scroll to "Environment Variables".
    *   Add these keys and values from your TiDB info:
        *   `DB_HOST` = (Your TiDB Host)
        *   `DB_USER` = (Your TiDB User)
        *   `DB_PASS` = (Your TiDB Password)
        *   `DB_NAME` = `cafe_db` (or whatever DB name TiDB gave you, default is usually `test` or you created `cafe_db`)
        *   `DB_PORT` = `4000` (TiDB uses 4000)
6.  Click **"Create Web Service"**.

---

Render will now:
1.  Clone your code.
2.  Build the Docker image (using the `Dockerfile` we made).
3.  Deploy it.
4.  Give you a link (`https://midnight-cafe-xyz.onrender.com`).

**This usually takes 3-5 minutes total.**
