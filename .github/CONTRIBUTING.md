# Contributing to NextCode Group

## 🎉 Thank you for considering contributing!

We welcome contributions from the community to help make NextCode Group better.

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Testing](#testing)
- [Pull Request Process](#pull-request-process)

## 📜 Code of Conduct

This project adheres to a code of conduct. By participating, you are expected to uphold this code.

## 🚀 Getting Started

### Prerequisites

- PHP 8.2+
- MySQL 8.0+
- Node.js 20+
- Composer
- npm

### Local Development Setup

1. **Clone the repository**
```bash
git clone https://github.com/yourusername/nextcode.git
cd nextcode
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Setup environment**
```bash
cp .env.example .env
# Edit .env with your local configuration
```

4. **Setup database**
```bash
mysql -u root -p < database/complete_database_structure.sql
```

5. **Build assets**
```bash
npm run dev
```

## 🔄 Development Workflow

### Branch Strategy

- `main` - Production-ready code
- `develop` - Development branch
- `feature/*` - New features
- `bugfix/*` - Bug fixes
- `hotfix/*` - Urgent production fixes

### Creating a Feature Branch

```bash
git checkout develop
git pull origin develop
git checkout -b feature/your-feature-name
```

## 💻 Coding Standards

### PHP Standards

We follow **PSR-12** coding standard:

```bash
# Check code style
composer cs

# Fix code style
composer cs-fix
```

### JavaScript Standards

We use ESLint with Standard config:

```bash
# Check JS code
npm run lint

# Fix JS issues
npm run lint:fix
```

### CSS Standards

We use Stylelint:

```bash
# Check CSS
npm run csslint

# Fix CSS issues
npm run csslint:fix
```

### General Guidelines

- Write clean, readable code
- Add comments for complex logic
- Use meaningful variable and function names
- Keep functions small and focused
- Follow DRY (Don't Repeat Yourself) principle

## 🧪 Testing

### Running Tests

```bash
# Run all tests
composer test
npm test

# Run specific test suite
vendor/bin/phpunit tests/Unit/DatabaseTest.php

# Run with coverage
composer test-coverage
```

### Writing Tests

- Write unit tests for new features
- Ensure tests pass before submitting PR
- Aim for >80% code coverage
- Test edge cases and error conditions

### Test Example

```php
public function testContactFormSubmission()
{
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'message' => 'Test message'
    ];
    
    $result = submitContactForm($data);
    
    $this->assertTrue($result['success']);
}
```

## 🔍 Pull Request Process

### Before Submitting

1. **Update from develop**
```bash
git checkout develop
git pull origin develop
git checkout your-feature-branch
git rebase develop
```

2. **Run all checks**
```bash
composer quality  # Runs all PHP checks
npm run validate  # Runs all JS/CSS checks
```

3. **Test thoroughly**
```bash
composer test
npm test
```

### PR Guidelines

- Fill out the PR template completely
- Reference related issues
- Include screenshots for UI changes
- Keep PRs focused and atomic
- Write clear commit messages
- Update documentation if needed

### PR Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
Describe testing performed

## Screenshots (if applicable)
Add screenshots here

## Checklist
- [ ] Code follows project standards
- [ ] Tests pass
- [ ] Documentation updated
- [ ] No breaking changes
```

## 📝 Commit Message Format

We use conventional commits:

```
type(scope): subject

body (optional)

footer (optional)
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Maintenance tasks

### Examples

```
feat(api): add portfolio filtering endpoint

- Added category filter
- Added search functionality
- Updated API documentation

Closes #123
```

```
fix(contact): resolve email validation issue

Fixed regex pattern for international email addresses
```

## 🐛 Reporting Bugs

### Bug Report Template

```markdown
## Bug Description
Clear description of the bug

## Steps to Reproduce
1. Go to...
2. Click on...
3. See error

## Expected Behavior
What should happen

## Actual Behavior
What actually happens

## Environment
- PHP version:
- Browser:
- OS:

## Screenshots
Add screenshots if applicable
```

## 💡 Feature Requests

We welcome feature requests! Please:

1. Check if feature already exists or requested
2. Provide clear use case
3. Explain why feature would be valuable
4. Consider implementation complexity

## 📚 Documentation

- Update README.md for major changes
- Add inline comments for complex code
- Update API documentation (openapi.yaml)
- Create/update guides in `/docs`

## 🙏 Questions?

Feel free to:
- Open an issue for discussion
- Ask in pull request comments
- Contact maintainers

## 📄 License

By contributing, you agree that your contributions will be licensed under the project's MIT License.

---

**Thank you for contributing to NextCode Group!** 🚀


