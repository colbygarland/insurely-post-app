import { test, expect } from '@playwright/test'

test('can view the login page', async ({ page }) => {
  await page.goto('http://localhost:8000/login')

  await expect(page.getByText('Email')).toBeVisible()
  await expect(page.getByText('Password')).toBeVisible()
  await expect(page.getByText('Log In')).toBeVisible()
})
