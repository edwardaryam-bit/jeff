# Deployment Guide - E-Waste Management System

## Option 1: Deploy to Vercel (Recommended for Node.js)

### Prerequisites
1. GitHub account (you already have this!)
2. Vercel account (free at vercel.com)
3. MongoDB Atlas account (free at mongodb.com/cloud/atlas)

### Step 1: Set Up MongoDB Atlas (Database)
1. Go to https://www.mongodb.com/cloud/atlas
2. Sign up for FREE tier
3. Create a cluster (free M0 cluster)
4. Get your connection string: `mongodb+srv://username:password@cluster0.xxxxx.mongodb.net/`
5. Create a database user and allow access from anywhere

### Step 2: Deploy to Vercel
1. Go to https://vercel.com and sign in with GitHub
2. Click "Import Project"
3. Select your "jeff" repository
4. Configure environment variables:
   ```
   MONGO_URI=mongodb+srv://your_user:your_password@cluster0.xxxxx.mongodb.net/?appName=Cluster0
   MONGO_ORG_ID=your_org_id
   ADMIN_PASSWORD=your_admin_password
   ```
5. Click "Deploy"

### Step 3: Your app will be live at:
```
https://your-project-name.vercel.app
```

---

## Option 2: Deploy to Replit (Fastest, No Credit Card)

### Steps:
1. Go to https://replit.com
2. Click "Import from GitHub"
3. Paste: `https://github.com/edwardaryam-bit/jeff`
4. Click "Import"
5. Add MongoDB connection (same as above)
6. Click "Run"
7. Your app will be live with a `.replit.dev` URL

---

## Option 3: Deploy to Railway (Free $5 Credits)

### Steps:
1. Go to https://railway.app
2. Sign in with GitHub
3. Create new project → GitHub repo
4. Select your "jeff" repo
5. Add MongoDB service
6. Set environment variables
7. Deploy

---

## Option 4: Self-Hosted (VPS like DigitalOcean)

### Cost: ~$5-6/month

### Steps:
1. Create a DigitalOcean account
2. Create a Droplet (Linux, Ubuntu 22.04)
3. SSH into your server
4. Install Node.js, MongoDB, and Nginx
5. Clone your GitHub repo
6. Run: `npm install && npm start`
7. Use Nginx as reverse proxy
8. Get a domain name (~$1-3/year)

---

## Quick Deployment Checklist

- [ ] MongoDB Atlas setup (get MONGO_URI)
- [ ] Add environment variables
- [ ] Push to GitHub
- [ ] Choose hosting (Vercel/Replit/Railway)
- [ ] Set environment variables in hosting
- [ ] Deploy
- [ ] Test at your live URL

---

## Recommended: Vercel + MongoDB Atlas

**Total Cost: FREE**

1. Vercel deployment: FREE
2. MongoDB Atlas: FREE tier
3. Your app: Live in 5 minutes!
